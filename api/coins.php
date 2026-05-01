<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
startSecureSession();

if (!isset($_SESSION['user_id'])) {
    jsonResponse(401, [ 'success' => false, 'message' => '未ログインです。' ]);
}

$userId = (int) $_SESSION['user_id'];
$pdo = getPdo();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT coins FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    $coins = isset($row['coins']) ? (int)$row['coins'] : 0;
    jsonResponse(200, [ 'success' => true, 'coins' => $coins ]);
}

if ($method === 'POST') {
    $body = readJsonBody();
    $action = $body['action'] ?? '';
    $amount = (int) ($body['amount'] ?? 0);
    if ($amount <= 0) {
        jsonResponse(400, [ 'success' => false, 'message' => '不正な金額です。' ]);
    }

    if ($action === 'buy') {
        // 増加
        $stmt = $pdo->prepare('UPDATE users SET coins = coins + ? WHERE id = ?');
        $stmt->execute([$amount, $userId]);
        $stmt = $pdo->prepare('SELECT coins FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $coins = (int) $stmt->fetchColumn();
        jsonResponse(200, [ 'success' => true, 'coins' => $coins ]);
    }

    if ($action === 'spend') {
        // 減算：トランザクションで安全に行う
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT coins FROM users WHERE id = ? FOR UPDATE');
            $stmt->execute([$userId]);
            $row = $stmt->fetch();
            $coins = isset($row['coins']) ? (int)$row['coins'] : 0;
            if ($coins < $amount) {
                $pdo->rollBack();
                jsonResponse(400, [ 'success' => false, 'message' => 'コインが不足しています。', 'coins' => $coins ]);
            }
            $stmt = $pdo->prepare('UPDATE users SET coins = coins - ? WHERE id = ?');
            $stmt->execute([$amount, $userId]);
            $stmt = $pdo->prepare('SELECT coins FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $newCoins = (int) $stmt->fetchColumn();
            $pdo->commit();
            jsonResponse(200, [ 'success' => true, 'coins' => $newCoins ]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            jsonResponse(500, [ 'success' => false, 'message' => 'サーバーエラー' ]);
        }
    }

    jsonResponse(400, [ 'success' => false, 'message' => '不明な action です。' ]);
}

jsonResponse(405, [ 'success' => false, 'message' => '許可されていないメソッドです。' ]);
