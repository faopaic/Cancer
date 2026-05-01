<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
startSecureSession();

if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    jsonResponse(401, [
        'success' => false,
        'message' => '未ログインです。',
    ]);
}

$pdo = getPdo();
$stmt = $pdo->prepare('SELECT coins FROM users WHERE id = ?');
$stmt->execute([ (int) $_SESSION['user_id'] ]);
$row = $stmt->fetch();
$coins = isset($row['coins']) ? (int)$row['coins'] : 0;

jsonResponse(200, [
    'success' => true,
    'user' => [
        'id' => (int) $_SESSION['user_id'],
        'username' => (string) $_SESSION['username'],
        'coins' => $coins,
    ],
]);
