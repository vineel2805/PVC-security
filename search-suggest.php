<?php
// ============================================================
//  search-suggest.php  — Live search API
//  Searches: Products (pname) + Brands (brandname) + Categories (cname)
//  Case-insensitive via LOWER()
//  DB columns confirmed from pvc.sql:
//    products  : pid, pname, pdescription, pcat (FK→category.cid), brandid (FK→brands.brandid), pimage, status
//    brands    : brandid, brandname, imagelink, status
//    category  : cid, cname, cimage, brandid, status
//
//  PERFORMANCE NOTE: this query type (LIKE '%term%') cannot use a normal
//  index because of the leading wildcard — MySQL must scan every row.
//  To make this fast you MUST add these indexes once (run in phpMyAdmin
//  or via SQL console — see bottom of this file for the exact statements).
//  Without the indexes, this PHP alone cannot fix the slowness.
// ============================================================

include 'connect.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q) < 1) {
    echo json_encode([]);
    exit;
}

// Cap query length to avoid abuse / huge LIKE scans on garbage input
if (mb_strlen($q) > 60) {
    $q = mb_substr($q, 0, 60);
}

$lower = strtolower($q);
$likeAny    = '%' . $lower . '%';
$likePrefix = $lower . '%';

$items = [];

// ── Helper: normalize an image path consistently across all result types ──
function pvc_normalize_image_path($path) {
    $path = (string) ($path ?? '');
    if ($path === '') {
        return '';
    }
    while (strpos($path, '../') === 0) {
        $path = substr($path, 3);
    }
    return $path;
}

// ── 1. PRODUCTS ──────────────────────────────────────────────
// Join: products → brands (on brandid), products → category (on pcat = cid)
// Visibility controlled by display_status only — stock status no longer
// hides items from search (out-of-stock items still findable, badge shown
// on the listing page instead).
$sqlP = "
    SELECT
        p.pid,
        p.pname,
        p.pimage,
        COALESCE(b.brandname, '') AS brandname,
        COALESCE(c.cname,     '') AS catname
    FROM products p
    LEFT JOIN brands   b ON b.brandid = p.brandid
    LEFT JOIN category c ON c.cid     = p.pcat
    WHERE p.display_status = 1
      AND (b.brandid IS NULL OR b.display_status = 1)
      AND (c.cid IS NULL OR c.display_status = 1)
      AND (
            LOWER(p.pname)     LIKE ?
         OR LOWER(b.brandname) LIKE ?
         OR LOWER(c.cname)     LIKE ?
      )
    ORDER BY
        CASE WHEN LOWER(p.pname) LIKE ? THEN 0 ELSE 1 END,
        p.pname ASC
    LIMIT 8
";
if ($stmtP = mysqli_prepare($con, $sqlP)) {
    mysqli_stmt_bind_param($stmtP, 'ssss', $likeAny, $likeAny, $likeAny, $likePrefix);
    mysqli_stmt_execute($stmtP);
    $rP = mysqli_stmt_get_result($stmtP);
    if ($rP) {
        while ($row = mysqli_fetch_assoc($rP)) {
            $sub = '';
            if ($row['brandname'] && $row['catname'])      $sub = $row['brandname'] . ' • ' . $row['catname'];
            elseif ($row['brandname'])                      $sub = $row['brandname'];
            elseif ($row['catname'])                        $sub = $row['catname'];

            $items[] = [
                'type'     => 'product',
                'label'    => (string) $row['pname'],
                'sublabel' => $sub,
                'price'    => '',
                'pimage'   => pvc_normalize_image_path($row['pimage']),
                'url'      => 'all-products.php?pid=' . urlencode($row['pid']),
            ];
        }
    }
    mysqli_stmt_close($stmtP);
}

// ── 2. BRANDS ────────────────────────────────────────────────
$sqlB = "
    SELECT brandid, brandname, COALESCE(imagelink, '') AS imagelink
    FROM   brands
    WHERE  display_status = 1
      AND  LOWER(brandname) LIKE ?
    ORDER BY
        CASE WHEN LOWER(brandname) LIKE ? THEN 0 ELSE 1 END,
        brandname ASC
    LIMIT 4
";
if ($stmtB = mysqli_prepare($con, $sqlB)) {
    mysqli_stmt_bind_param($stmtB, 'ss', $likeAny, $likePrefix);
    mysqli_stmt_execute($stmtB);
    $rB = mysqli_stmt_get_result($stmtB);
    if ($rB) {
        while ($row = mysqli_fetch_assoc($rB)) {
            $items[] = [
                'type'     => 'brand',
                'label'    => (string) $row['brandname'],
                'sublabel' => 'Brand',
                'price'    => '',
                'pimage'   => pvc_normalize_image_path($row['imagelink']),
                'url'      => 'all-products.php?brand=' . urlencode($row['brandid']),
            ];
        }
    }
    mysqli_stmt_close($stmtB);
}

// ── 3. CATEGORIES ────────────────────────────────────────────
// Group by UPPER(TRIM(cname)) to merge duplicates (same cname across brands)
$sqlC = "
    SELECT
        MIN(cid)    AS cid,
        cname,
        MIN(cimage) AS cimage
    FROM   category
    WHERE  display_status = 1
      AND  LOWER(cname) LIKE ?
    GROUP BY UPPER(TRIM(cname))
    ORDER BY
        CASE WHEN LOWER(cname) LIKE ? THEN 0 ELSE 1 END,
        cname ASC
    LIMIT 4
";
if ($stmtC = mysqli_prepare($con, $sqlC)) {
    mysqli_stmt_bind_param($stmtC, 'ss', $likeAny, $likePrefix);
    mysqli_stmt_execute($stmtC);
    $rC = mysqli_stmt_get_result($stmtC);
    if ($rC) {
        while ($row = mysqli_fetch_assoc($rC)) {
            $items[] = [
                'type'     => 'category',
                'label'    => (string) $row['cname'],
                'sublabel' => 'Category',
                'price'    => '',
                'pimage'   => pvc_normalize_image_path($row['cimage']),
                'url'      => 'all-categories.php?catname=' . urlencode($row['cname']),
            ];
        }
    }
    mysqli_stmt_close($stmtC);
}

echo json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// ============================================================
//  REQUIRED ONE-TIME DB CHANGES — run these once in phpMyAdmin
//  (SQL tab) or your MySQL console. These do NOT run automatically
//  from this PHP file — copy/paste them yourself, once:
//
//  ALTER TABLE products ADD INDEX idx_search_pname (display_status, pname);
//  ALTER TABLE products ADD INDEX idx_search_brandid (brandid);
//  ALTER TABLE products ADD INDEX idx_search_pcat (pcat);
//  ALTER TABLE brands   ADD INDEX idx_search_brandname (display_status, brandname);
//  ALTER TABLE category ADD INDEX idx_search_cname (display_status, cname);
//
//  These speed up the JOIN and the display_status filter significantly.
//  They do NOT fix the '%term%' wildcard scan itself — that part will
//  always require scanning matching rows. If your products table grows
//  past a few thousand rows and it's still slow after adding these
//  indexes, the real fix is a MySQL FULLTEXT index on pname (and a
//  MATCH...AGAINST query instead of LIKE), which I can set up next.
// ============================================================