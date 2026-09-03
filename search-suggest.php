<?php

declare(strict_types=1);

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/search-engine.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    echo json_encode([]);
    exit;
}

$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

if ($q === '') {
    echo json_encode([]);
    exit;
}

echo json_encode(
    pvc_search_suggest_items($con, $q),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
