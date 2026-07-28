<?php
require_once 'config/db.php';

header('Content-Type: application/json');

$brandid = trim($_GET['brandid'] ?? '');

if ($brandid === '') {
    echo json_encode([]);
    exit;
}

try {
    // Adjust WHERE clause if your schema links brands to categories differently
    $stmt = $pdo->prepare("
        SELECT cid, cname 
        FROM category 
        WHERE brandid = :brandid 
        ORDER BY cname ASC
    ");
    $stmt->execute([':brandid' => $brandid]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode([]);
}