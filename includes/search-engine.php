<?php

declare(strict_types=1);

/**
 * PVC Search Engine
 *
 * Search pipeline:
 *
 * Query
 *  → Normalize
 *  → Tokenize
 *  → Intent
 *  → Entity resolution
 *  → Candidate retrieval
 *  → Match analysis
 *  → Scoring
 *  → Deterministic ranking
 *
 * Expected schema:
 *
 * products:
 *   pid, pname, pdescription, pimage, brandid, pcat, display_status
 *
 * brands:
 *   brandid, brandname, display_status
 *
 * category:
 *   cid, cname, cimage, display_status
 */


/* =========================================================
 * CONFIG
 * ========================================================= */

function pvc_search_config(): array
{
    return [
        'max_query_length' => 80,
        'max_tokens'       => 8,

        'candidate_limit'  => 200,
        'result_limit'     => 100,

        /*
         * Ranking weights.
         *
         * Higher = stronger relevance signal.
         */
        'score' => [
            'exact_model'          => 12000,
            'exact_name'           => 10000,
            'exact_phrase'         => 7000,

            'all_tokens'           => 4000,

            'brand_exact'          => 3500,
            'brand_prefix'         => 1800,

            'category_exact'       => 3200,
            'category_prefix'      => 1600,

            'name_exact_token'     => 1800,
            'name_prefix_token'    => 900,
            'name_partial_token'   => 300,

            'description_token'    => 100,

            'partial_coverage'     => 1800,
        ],
    ];
}


/* =========================================================
 * NORMALIZATION
 * ========================================================= */

function pvc_search_max_length(): int
{
    return (int) pvc_search_config()['max_query_length'];
}


function pvc_search_max_tokens(): int
{
    return (int) pvc_search_config()['max_tokens'];
}


function pvc_search_normalize(string $query): string
{
    $query = html_entity_decode(
        $query,
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );

    /*
     * Remove control characters.
     */
    $query = preg_replace(
        '/[\x00-\x1F\x7F]/u',
        ' ',
        $query
    ) ?? '';

    /*
     * Convert to lowercase.
     */
    $query = mb_strtolower(
        $query,
        'UTF-8'
    );

    /*
     * Preserve characters commonly found in
     * ecommerce product/model identifiers.
     */
    $query = preg_replace(
        '/[^\p{L}\p{N}\s._\/-]/u',
        ' ',
        $query
    ) ?? '';

    /*
     * Collapse whitespace.
     */
    $query = preg_replace(
        '/\s+/u',
        ' ',
        $query
    ) ?? '';

    $query = trim($query);

    /*
     * Prevent unnecessarily large search requests.
     */
    if (
        mb_strlen($query, 'UTF-8')
        > pvc_search_max_length()
    ) {
        $query = mb_substr(
            $query,
            0,
            pvc_search_max_length(),
            'UTF-8'
        );

        $query = trim($query);
    }

    return $query;
}


/* =========================================================
 * TOKENIZATION
 * ========================================================= */

function pvc_search_tokens(string $query): array
{
    if ($query === '') {
        return [];
    }

    $parts = preg_split(
        '/\s+/u',
        $query,
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    if (!$parts) {
        return [];
    }

    $tokens = [];

    foreach ($parts as $token) {

        $token = trim($token);

        if ($token === '') {
            continue;
        }

        /*
         * Ignore one-character alphabetic noise.
         *
         * Keep numeric one-character tokens because
         * product/model searches can contain them.
         */
        if (
            mb_strlen($token, 'UTF-8') < 2 &&
            !preg_match('/^\d$/', $token)
        ) {
            continue;
        }

        $tokens[] = $token;
    }

    $tokens = array_values(
        array_unique($tokens)
    );

    return array_slice(
        $tokens,
        0,
        pvc_search_max_tokens()
    );
}


/* =========================================================
 * TEXT HELPERS
 * ========================================================= */

function pvc_search_rank_text(string $value): string
{
    $value = html_entity_decode(
        $value,
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );

    $value = strip_tags($value);

    $value = mb_strtolower(
        $value,
        'UTF-8'
    );

    $value = preg_replace(
        '/\s+/u',
        ' ',
        $value
    ) ?? '';

    return trim($value);
}


/**
 * Compact representation used for product/model matching.
 *
 * DS-2CE70DF0T-PFS
 * DS 2CE70DF0T PFS
 *
 * both become:
 *
 * ds2ce70df0tpfs
 */
function pvc_search_compact(string $value): string
{
    $value = pvc_search_rank_text($value);

    return preg_replace(
        '/[^\p{L}\p{N}]/u',
        '',
        $value
    ) ?? '';
}


/**
 * Split product text into meaningful words.
 */
function pvc_search_word_tokens(string $value): array
{
    $value = pvc_search_rank_text($value);

    if ($value === '') {
        return [];
    }

    $parts = preg_split(
        '/[\s._\/-]+/u',
        $value,
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    return $parts
        ? array_values(array_unique($parts))
        : [];
}


/**
 * Determine whether a token looks like a product/model code.
 */
function pvc_search_is_model_token(string $token): bool
{
    return (
        preg_match('/[a-z]/i', $token) === 1 &&
        preg_match('/\d/', $token) === 1
    );
}


/* =========================================================
 * QUERY / INTENT
 * ========================================================= */

function pvc_search_detect_intent(
    string $normalized,
    array $tokens
): array {
    $modelTokens = [];

    foreach ($tokens as $token) {
        if (pvc_search_is_model_token($token)) {
            $modelTokens[] = $token;
        }
    }

    if ($normalized === '') {
        $type = 'empty';
    } elseif ($modelTokens !== []) {
        $type = 'product_code';
    } elseif (count($tokens) === 1) {
        $type = 'single_term';
    } else {
        $type = 'multi_term';
    }

    return [
        'type' => $type,

        'has_model' => (
            $modelTokens !== []
        ),

        'has_brand_like_token' => (
            count($tokens) === 1 &&
            preg_match(
                '/^\p{L}+$/u',
                $tokens[0] ?? ''
            ) === 1
        ),

        'token_count' => count($tokens),

        'model_tokens' => $modelTokens,
    ];
}


/* =========================================================
 * SQL HELPERS
 * ========================================================= */

function pvc_search_fetch_all(
    mysqli $con,
    string $sql,
    string $types = '',
    array $params = []
): array {
    $stmt = mysqli_prepare(
        $con,
        $sql
    );

    if (!$stmt) {
        error_log(
            'PVC search prepare failed: ' .
            mysqli_error($con)
        );

        return [];
    }

    if ($types !== '') {
        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$params
        );
    }

    if (!mysqli_stmt_execute($stmt)) {
        error_log(
            'PVC search execute failed: ' .
            mysqli_stmt_error($stmt)
        );

        mysqli_stmt_close($stmt);

        return [];
    }

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        mysqli_stmt_close($stmt);

        return [];
    }

    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $rows;
}


function pvc_search_merge_products(
    array &$products,
    array $rows
): void {
    foreach ($rows as $row) {

        if (!isset($row['pid'])) {
            continue;
        }

        $id = (string) $row['pid'];

        if (!isset($products[$id])) {
            $products[$id] = $row;
        }
    }
}


/* =========================================================
 * ENTITY RESOLUTION
 * ========================================================= */

function pvc_search_resolve_entities(
    mysqli $con,
    array $tokens
): array {
    $resolved = [
        'brands' => [],
        'categories' => [],
        'products' => [],
    ];

    if ($tokens === []) {
        return $resolved;
    }

    /*
     * -----------------------------------------------------
     * BRANDS
     * -----------------------------------------------------
     */

    $brandSql = "
        SELECT
            brandid,
            brandname
        FROM brands
        WHERE
            display_status = 1
            AND (
                LOWER(brandname) = LOWER(?)
                OR LOWER(brandname)
                    LIKE CONCAT(LOWER(?), '%')
            )
        ORDER BY
            CASE
                WHEN LOWER(brandname) = LOWER(?)
                THEN 0
                ELSE 1
            END,
            brandname ASC
        LIMIT 10
    ";

    $brandStmt = mysqli_prepare(
        $con,
        $brandSql
    );

    if ($brandStmt) {

        foreach ($tokens as $token) {

            mysqli_stmt_bind_param(
                $brandStmt,
                'sss',
                $token,
                $token,
                $token
            );

            if (!mysqli_stmt_execute($brandStmt)) {
                error_log(
                    'PVC brand resolution failed: ' .
                    mysqli_stmt_error($brandStmt)
                );

                continue;
            }

            $result = mysqli_stmt_get_result(
                $brandStmt
            );

            if (!$result) {
                continue;
            }

            while ($row = mysqli_fetch_assoc($result)) {

                $id = (string) $row['brandid'];

                $resolved['brands'][$id] = [
                    'brandid' => $id,
                    'brandname' => (string) $row['brandname'],
                    'matched_token' => $token,
                ];
            }
        }

        mysqli_stmt_close($brandStmt);
    }


    /*
     * -----------------------------------------------------
     * CATEGORIES
     * -----------------------------------------------------
     */

    $categorySql = "
        SELECT
            cid,
            cname
        FROM category
        WHERE
            display_status = 1
            AND (
                LOWER(cname) = LOWER(?)
                OR LOWER(cname)
                    LIKE CONCAT(LOWER(?), '%')
            )
        ORDER BY
            CASE
                WHEN LOWER(cname) = LOWER(?)
                THEN 0
                ELSE 1
            END,
            cname ASC
        LIMIT 10
    ";

    $categoryStmt = mysqli_prepare(
        $con,
        $categorySql
    );

    if ($categoryStmt) {

        foreach ($tokens as $token) {

            mysqli_stmt_bind_param(
                $categoryStmt,
                'sss',
                $token,
                $token,
                $token
            );

            if (!mysqli_stmt_execute($categoryStmt)) {
                error_log(
                    'PVC category resolution failed: ' .
                    mysqli_stmt_error($categoryStmt)
                );

                continue;
            }

            $result = mysqli_stmt_get_result(
                $categoryStmt
            );

            if (!$result) {
                continue;
            }

            while ($row = mysqli_fetch_assoc($result)) {

                $id = (string) $row['cid'];

                $resolved['categories'][$id] = [
                    'cid' => $id,
                    'cname' => (string) $row['cname'],
                    'matched_token' => $token,
                ];
            }
        }

        mysqli_stmt_close($categoryStmt);
    }


    /*
     * -----------------------------------------------------
     * EXACT PRODUCT / MODEL
     * -----------------------------------------------------
     */

    $productSql = "
        SELECT
            p.pid,
            p.pname,
            p.brandid,
            p.pcat
        FROM products p
        WHERE
            p.display_status = 1
            AND (
                LOWER(p.pid) = LOWER(?)
                OR LOWER(p.pname) = LOWER(?)
            )
        LIMIT 10
    ";

    $productStmt = mysqli_prepare(
        $con,
        $productSql
    );

    if ($productStmt) {

        foreach ($tokens as $token) {

            mysqli_stmt_bind_param(
                $productStmt,
                'ss',
                $token,
                $token
            );

            if (!mysqli_stmt_execute($productStmt)) {
                error_log(
                    'PVC product resolution failed: ' .
                    mysqli_stmt_error($productStmt)
                );

                continue;
            }

            $result = mysqli_stmt_get_result(
                $productStmt
            );

            if (!$result) {
                continue;
            }

            while ($row = mysqli_fetch_assoc($result)) {

                $id = (string) $row['pid'];

                $resolved['products'][$id] = [
                    'pid' => $id,
                    'pname' => (string) $row['pname'],
                    'brandid' => (string) $row['brandid'],
                    'pcat' => (string) $row['pcat'],
                    'matched_token' => $token,
                ];
            }
        }

        mysqli_stmt_close($productStmt);
    }


    return [
        'brands' => array_values(
            $resolved['brands']
        ),

        'categories' => array_values(
            $resolved['categories']
        ),

        'products' => array_values(
            $resolved['products']
        ),
    ];
}


/* =========================================================
 * PRODUCT BASE QUERY
 * ========================================================= */

function pvc_search_product_base_sql(): string
{
    return "
        SELECT
            p.pid,
            p.pname,
            p.pdescription,
            p.pimage,
            p.brandid,
            p.pcat,

            COALESCE(
                b.brandname,
                ''
            ) AS brandname,

            COALESCE(
                c.cname,
                ''
            ) AS catname

        FROM products p

        LEFT JOIN brands b
            ON b.brandid = p.brandid

        LEFT JOIN category c
            ON c.cid = p.pcat

        WHERE
            p.display_status = 1

            AND (
                b.brandid IS NULL
                OR b.display_status = 1
            )

            AND (
                c.cid IS NULL
                OR c.display_status = 1
            )
    ";
}


/* =========================================================
 * CANDIDATE RETRIEVAL
 * ========================================================= */

function pvc_search_product_candidates(
    mysqli $con,
    array $tokens,
    ?array $entities = null
): array {
    if ($tokens === []) {
        return [];
    }

    $config = pvc_search_config();

    if ($entities === null) {
        $entities = pvc_search_resolve_entities(
            $con,
            $tokens
        );
    }

    $products = [];


    /*
     * -----------------------------------------------------
     * EXACTLY RESOLVED PRODUCTS
     * -----------------------------------------------------
     */

    foreach (
        $entities['products'] ?? []
        as $product
    ) {
        if (isset($product['pid'])) {
            $products[(string) $product['pid']] = $product;
        }
    }


    /*
     * -----------------------------------------------------
     * FULLTEXT
     * -----------------------------------------------------
     *
     * Each token is treated as a prefix.
     *
     * Example:
     *
     * hik camera
     *
     * → hik* camera*
     *
     * This is retrieval only.
     *
     * Final relevance is calculated separately.
     */

    $fulltextTokens = [];

    foreach ($tokens as $token) {

        $safeToken = preg_replace(
            '/[+\-<>()~*"@]/u',
            '',
            $token
        );

        $safeToken = trim(
            (string) $safeToken
        );

        if ($safeToken !== '') {
            $fulltextTokens[] =
                $safeToken . '*';
        }
    }

    $searchQuery = implode(
        ' ',
        $fulltextTokens
    );


    if ($searchQuery !== '') {

        $sql =
            pvc_search_product_base_sql() .
            "
                AND MATCH(
                    p.pname,
                    p.pdescription
                )
                AGAINST(
                    ?
                    IN BOOLEAN MODE
                )

                ORDER BY
                    MATCH(
                        p.pname,
                        p.pdescription
                    )
                    AGAINST(
                        ?
                        IN BOOLEAN MODE
                    ) DESC,

                    p.pname ASC,
                    p.pid ASC

                LIMIT ?
            ";

        $rows = pvc_search_fetch_all(
            $con,
            $sql,
            'ssi',
            [
                $searchQuery,
                $searchQuery,
                (int) $config['candidate_limit'],
            ]
        );

        pvc_search_merge_products(
            $products,
            $rows
        );
    }


    /*
     * -----------------------------------------------------
     * BRAND CANDIDATES
     * -----------------------------------------------------
     */

    foreach (
        $entities['brands'] ?? []
        as $brand
    ) {

        if (!isset($brand['brandid'])) {
            continue;
        }

        $rows = pvc_search_fetch_all(
            $con,
            pvc_search_product_base_sql() .
            "
                AND p.brandid = ?
                ORDER BY
                    p.pname ASC,
                    p.pid ASC
                LIMIT ?
            ",
            'ii',
            [
                (int) $brand['brandid'],
                (int) $config['candidate_limit'],
            ]
        );

        pvc_search_merge_products(
            $products,
            $rows
        );
    }


    /*
     * -----------------------------------------------------
     * CATEGORY CANDIDATES
     * -----------------------------------------------------
     */

    foreach (
        $entities['categories'] ?? []
        as $category
    ) {

        if (!isset($category['cid'])) {
            continue;
        }

        $rows = pvc_search_fetch_all(
            $con,
            pvc_search_product_base_sql() .
            "
                AND p.pcat = ?
                ORDER BY
                    p.pname ASC,
                    p.pid ASC
                LIMIT ?
            ",
            'ii',
            [
                (int) $category['cid'],
                (int) $config['candidate_limit'],
            ]
        );

        pvc_search_merge_products(
            $products,
            $rows
        );
    }


    /*
     * -----------------------------------------------------
     * LIKE FALLBACK
     * -----------------------------------------------------
     *
     * Important:
     *
     * "hikvision camera"
     *
     * does NOT require both tokens to be inside pname.
     *
     * hikvision may exist in brandname while camera
     * exists in catname/pname.
     *
     * Therefore retrieval is OR-based.
     */

    $conditions = [];
    $params = [];
    $types = '';

    foreach ($tokens as $token) {

        $pattern =
            '%' .
            mb_strtolower(
                $token,
                'UTF-8'
            ) .
            '%';

        $conditions[] = "
            LOWER(CAST(p.pid AS CHAR)) LIKE ?
            OR LOWER(p.pname) LIKE ?
            OR LOWER(p.pdescription) LIKE ?
            OR LOWER(
                COALESCE(
                    b.brandname,
                    ''
                )
            ) LIKE ?
            OR LOWER(
                COALESCE(
                    c.cname,
                    ''
                )
            ) LIKE ?
        ";

        for ($i = 0; $i < 5; $i++) {
            $params[] = $pattern;
            $types .= 's';
        }
    }


    if ($conditions !== []) {

        $sql =
            pvc_search_product_base_sql() .
            "
                AND (
                    " .
                    implode(
                        ' OR ',
                        array_map(
                            static fn(
                                string $condition
                            ): string =>
                                '(' .
                                $condition .
                                ')',
                            $conditions
                        )
                    ) .
                    "
                )

                ORDER BY
                    p.pname ASC,
                    p.pid ASC

                LIMIT ?
            ";

        $params[] =
            (int) $config['candidate_limit'];

        $types .= 'i';

        $rows = pvc_search_fetch_all(
            $con,
            $sql,
            $types,
            $params
        );

        pvc_search_merge_products(
            $products,
            $rows
        );
    }


    /*
     * Final candidate bound.
     */
    if (
        count($products)
        > $config['candidate_limit']
    ) {
        $products = array_slice(
            $products,
            0,
            $config['candidate_limit']
        );
    }

    return array_values($products);
}


/* =========================================================
 * MATCH ANALYSIS
 * ========================================================= */

function pvc_search_token_match(
    string $token,
    string $text
): string {
    $token = pvc_search_rank_text($token);
    $text = pvc_search_rank_text($text);

    if (
        $token === '' ||
        $text === ''
    ) {
        return 'none';
    }

    /*
     * Complete field match.
     */
    if ($text === $token) {
        return 'exact';
    }

    /*
     * Exact word.
     */
    $words = pvc_search_word_tokens($text);

    foreach ($words as $word) {
        if ($word === $token) {
            return 'exact';
        }
    }

    /*
     * Prefix word match.
     *
     * "hik" → "hikvision"
     */
    foreach ($words as $word) {

        if (
            mb_strlen($token, 'UTF-8') >= 2 &&
            str_starts_with(
                $word,
                $token
            )
        ) {
            return 'prefix';
        }
    }

    /*
     * General substring.
     */
    if (str_contains($text, $token)) {
        return 'partial';
    }

    return 'none';
}


/**
 * Compare a model/code token against product name
 * and product ID while ignoring formatting separators.
 */
function pvc_search_model_match(
    string $token,
    string $name,
    string $pid
): string {
    $token = pvc_search_compact($token);

    if ($token === '') {
        return 'none';
    }

    $name = pvc_search_compact($name);
    $pid  = pvc_search_compact($pid);

    if (
        $token === $name ||
        $token === $pid
    ) {
        return 'exact';
    }

    if (
        str_starts_with($name, $token) ||
        str_starts_with($pid, $token)
    ) {
        return 'prefix';
    }

    return 'none';
}


function pvc_search_analyze_match(
    array $product,
    array $tokens,
    string $normalized
): array {
    $name = pvc_search_rank_text(
        (string) ($product['pname'] ?? '')
    );

    $description = pvc_search_rank_text(
        (string) ($product['pdescription'] ?? '')
    );

    $brand = pvc_search_rank_text(
        (string) ($product['brandname'] ?? '')
    );

    $category = pvc_search_rank_text(
        (string) ($product['catname'] ?? '')
    );

    $pid = pvc_search_rank_text(
        (string) ($product['pid'] ?? '')
    );


    $match = [
        'exact_model' => false,
        'exact_name' => false,
        'exact_phrase' => false,

        'brand_exact' => false,
        'category_exact' => false,

        'matched_tokens' => 0,

        'name_exact_tokens' => 0,
        'name_prefix_tokens' => 0,
        'name_partial_tokens' => 0,

        'brand_exact_tokens' => 0,
        'brand_prefix_tokens' => 0,

        'category_exact_tokens' => 0,
        'category_prefix_tokens' => 0,

        'description_tokens' => 0,

        'tokens' => [],
    ];


    /*
     * Exact model.
     */
    if (
        pvc_search_model_match(
            $normalized,
            $name,
            $pid
        ) === 'exact'
    ) {
        $match['exact_model'] = true;
    }


    /*
     * Exact product name.
     */
    if (
        $name === $normalized
    ) {
        $match['exact_name'] = true;
    }


    /*
     * Exact phrase inside product name.
     */
    if (
        $normalized !== '' &&
        str_contains(
            $name,
            $normalized
        )
    ) {
        $match['exact_phrase'] = true;
    }


    /*
     * Exact brand/category.
     */
    $match['brand_exact'] =
        $brand !== '' &&
        $brand === $normalized;

    $match['category_exact'] =
        $category !== '' &&
        $category === $normalized;


    /*
     * Analyze every query token.
     */
    foreach ($tokens as $token) {

        $nameMatch = pvc_search_token_match(
            $token,
            $name
        );

        $brandMatch = pvc_search_token_match(
            $token,
            $brand
        );

        $categoryMatch = pvc_search_token_match(
            $token,
            $category
        );

        $descriptionMatch =
            pvc_search_token_match(
                $token,
                $description
            );

        $modelMatch =
            pvc_search_model_match(
                $token,
                $name,
                $pid
            );


        $matched =
            $nameMatch !== 'none' ||
            $brandMatch !== 'none' ||
            $categoryMatch !== 'none' ||
            $descriptionMatch !== 'none' ||
            $modelMatch !== 'none';


        if ($matched) {
            $match['matched_tokens']++;
        }


        if ($nameMatch === 'exact') {
            $match['name_exact_tokens']++;
        } elseif ($nameMatch === 'prefix') {
            $match['name_prefix_tokens']++;
        } elseif ($nameMatch === 'partial') {
            $match['name_partial_tokens']++;
        }


        if ($brandMatch === 'exact') {
            $match['brand_exact_tokens']++;
        } elseif ($brandMatch === 'prefix') {
            $match['brand_prefix_tokens']++;
        }


        if ($categoryMatch === 'exact') {
            $match['category_exact_tokens']++;
        } elseif ($categoryMatch === 'prefix') {
            $match['category_prefix_tokens']++;
        }


        if ($descriptionMatch !== 'none') {
            $match['description_tokens']++;
        }


        $match['tokens'][] = [
            'token' => $token,
            'model' => $modelMatch,
            'name' => $nameMatch,
            'brand' => $brandMatch,
            'category' => $categoryMatch,
            'description' => $descriptionMatch,
        ];
    }


    return $match;
}


/* =========================================================
 * SCORING
 * ========================================================= */

function pvc_search_product_score(
    array $product,
    array $tokens,
    string $normalized
): int {
    $config = pvc_search_config();
    $weights = $config['score'];

    $match = pvc_search_analyze_match(
        $product,
        $tokens,
        pvc_search_rank_text($normalized)
    );


    /*
     * -----------------------------------------------------
     * EXACT MODEL
     * -----------------------------------------------------
     *
     * A model/code lookup should dominate normal text
     * relevance.
     */
    if ($match['exact_model']) {
        return $weights['exact_model'];
    }


    /*
     * -----------------------------------------------------
     * EXACT PRODUCT NAME
     * -----------------------------------------------------
     */
    if ($match['exact_name']) {
        return $weights['exact_name'];
    }


    $tokenCount = count($tokens);

    if (
        $tokenCount === 0 ||
        $match['matched_tokens'] === 0
    ) {
        return 0;
    }


    $score = 0;


    /*
     * -----------------------------------------------------
     * PHRASE
     * -----------------------------------------------------
     */
    if ($match['exact_phrase']) {
        $score += $weights['exact_phrase'];
    }


    /*
     * -----------------------------------------------------
     * TOKEN COVERAGE
     * -----------------------------------------------------
     */

    $coverage =
        $match['matched_tokens']
        / $tokenCount;


    if ($coverage >= 1.0) {
        $score += $weights['all_tokens'];
    } else {
        $score += (int) round(
            $coverage *
            $weights['partial_coverage']
        );
    }


    /*
     * -----------------------------------------------------
     * PRODUCT NAME
     * -----------------------------------------------------
     */

    $score +=
        $match['name_exact_tokens']
        * $weights['name_exact_token'];

    $score +=
        $match['name_prefix_tokens']
        * $weights['name_prefix_token'];

    $score +=
        $match['name_partial_tokens']
        * $weights['name_partial_token'];


    /*
     * -----------------------------------------------------
     * BRAND
     * -----------------------------------------------------
     */

    $score +=
        $match['brand_exact_tokens']
        * $weights['brand_exact'];

    $score +=
        $match['brand_prefix_tokens']
        * $weights['brand_prefix'];


    /*
     * -----------------------------------------------------
     * CATEGORY
     * -----------------------------------------------------
     */

    $score +=
        $match['category_exact_tokens']
        * $weights['category_exact'];

    $score +=
        $match['category_prefix_tokens']
        * $weights['category_prefix'];


    /*
     * -----------------------------------------------------
     * DESCRIPTION
     * -----------------------------------------------------
     */

    $score +=
        $match['description_tokens']
        * $weights['description_token'];


    /*
     * -----------------------------------------------------
     * Exact entity bonuses
     * -----------------------------------------------------
     */

    if ($match['brand_exact']) {
        $score += $weights['brand_exact'];
    }

    if ($match['category_exact']) {
        $score += $weights['category_exact'];
    }


    return max(
        0,
        $score
    );
}


/* =========================================================
 * RANKING
 * ========================================================= */

function pvc_search_rank_products(
    array $products,
    array $tokens,
    string $normalized
): array {
    $config = pvc_search_config();

    foreach ($products as &$product) {

        $product['_score'] =
            pvc_search_product_score(
                $product,
                $tokens,
                $normalized
            );
    }

    unset($product);


    /*
     * Remove products that have no relevance signal.
     */
    $products = array_values(
        array_filter(
            $products,
            static function (
                array $product
            ): bool {
                return (
                    (int) (
                        $product['_score'] ?? 0
                    )
                ) > 0;
            }
        )
    );


    /*
     * Deterministic ordering.
     *
     * Score
     * → token coverage
     * → exact model
     * → name
     * → pid
     */
    usort(
        $products,
        static function (
            array $a,
            array $b
        ): int {

            $scoreA =
                (int) (
                    $a['_score'] ?? 0
                );

            $scoreB =
                (int) (
                    $b['_score'] ?? 0
                );

            if ($scoreA !== $scoreB) {
                return $scoreB <=> $scoreA;
            }


            $matchA =
                pvc_search_analyze_match(
                    $a,
                    [],
                    ''
                );

            $matchB =
                pvc_search_analyze_match(
                    $b,
                    [],
                    ''
                );


            /*
             * Product name is the final deterministic
             * relevance tie-break.
             */
            $nameComparison =
                strcasecmp(
                    (string) (
                        $a['pname'] ?? ''
                    ),
                    (string) (
                        $b['pname'] ?? ''
                    )
                );

            if ($nameComparison !== 0) {
                return $nameComparison;
            }


            return strcasecmp(
                (string) (
                    $a['pid'] ?? ''
                ),
                (string) (
                    $b['pid'] ?? ''
                )
            );
        }
    );


    return array_slice(
        $products,
        0,
        (int) $config['result_limit']
    );
}


/* =========================================================
 * COMPLETE SEARCH
 * ========================================================= */

/**
 * Main public search function.
 *
 * Application code should normally call this function
 * instead of manually running each search stage.
 */
function pvc_search(
    mysqli $con,
    string $query
): array {
    $normalized =
        pvc_search_normalize($query);

    $tokens =
        pvc_search_tokens($normalized);

    $intent =
        pvc_search_detect_intent(
            $normalized,
            $tokens
        );


    if ($tokens === []) {
        return [
            'query' => $query,
            'normalized' => $normalized,
            'tokens' => [],
            'intent' => $intent,

            'entities' => [
                'brands' => [],
                'categories' => [],
                'products' => [],
            ],

            'products' => [],
        ];
    }


    /*
     * Resolve database entities once.
     */
    $entities =
        pvc_search_resolve_entities(
            $con,
            $tokens
        );


    /*
     * Retrieve candidates.
     */
    $products =
        pvc_search_product_candidates(
            $con,
            $tokens,
            $entities
        );


    /*
     * Rank candidates.
     */
    $products =
        pvc_search_rank_products(
            $products,
            $tokens,
            $normalized
        );


    return [
        'query' => $query,

        'normalized' => $normalized,

        'tokens' => $tokens,

        'intent' => $intent,

        'entities' => $entities,

        'products' => $products,
    ];
}