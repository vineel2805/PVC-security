<?php

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/search-engine.php';

$tests = [
'hik',
'hikvision 5',
'5mp dome',
'dome camera',
'cp',
'cp plus',
'ds 2ce70',
'wifi camera',
'hikvision dvr',
'hikvision nvr',
'5mp camera'
];

foreach ($tests as $input) {

    $normalized = pvc_search_normalize($input);
    $tokens = pvc_search_tokens($normalized);
    $intent = pvc_search_detect_intent(
        $normalized,
        $tokens
    );
    $entities = pvc_search_resolve_entities(
    $con,
    $tokens
    );
    echo '<hr>';

    echo '<h3>';
    echo htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    echo '</h3>';

    echo '<strong>Normalized:</strong> ';
    echo htmlspecialchars(
        $normalized,
        ENT_QUOTES,
        'UTF-8'
    );

    echo '<br>';

    echo '<strong>Tokens:</strong> ';
    echo htmlspecialchars(
        json_encode(
            $tokens,
            JSON_UNESCAPED_UNICODE
        ),
        ENT_QUOTES,
        'UTF-8'
    );

    echo '<h4>Products</h4>';

  $products = pvc_search_product_candidates(
    $con,
    $tokens
);

$products = pvc_search_rank_products(
    $products,
    $tokens,
    $normalized
);

    echo '<strong>Count:</strong> ';
    echo count($products);

    echo '<br><br>';

    foreach ($products as $product) {
               echo '<strong>Score:</strong> ';
echo (int) $product['_score'];
echo '<br>'; 
        echo htmlspecialchars(
            $product['pname'],
            ENT_QUOTES,
            'UTF-8'
        );

        echo ' | ';

        echo htmlspecialchars(
            $product['brandname'],
            ENT_QUOTES,
            'UTF-8'
        );

        echo ' | ';

        echo htmlspecialchars(
            $product['catname'],
            ENT_QUOTES,
            'UTF-8'
        );

        echo '<br>';
    }
    $intent = pvc_search_detect_intent(
    $normalized,
    $tokens
);
}



echo '<strong>Intent:</strong> ';

echo htmlspecialchars(
    json_encode(
        $intent,
        JSON_UNESCAPED_UNICODE
    ),
    ENT_QUOTES,
    'UTF-8'
);

echo '<br><br>';