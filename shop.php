<?php
declare(strict_types=1);

$pageTitle = 'コイン購入';
$bodyClass = 'shop-page'; // 既存CSSの main padding-top: 80px が適用されます
$scriptFile = 'shop';
$assetBase = 'assets';
$brandTitle = 'Cancer Gacha';
$subtitle = 'コインショップ';
$logoutLabel = 'ログアウト';

$plans = [
    [
        'name' => 'ミニパック', 
        'amount' => 10, 
        'price' => 120, 
        'bonus' => 0, 
        'icon' => '🪙'
    ],
    [
        'name' => 'バリューパック', 
        'amount' => 30, 
        'price' => 320, 
        'bonus' => 5, 
        'icon' => '💰', 
        'recommend' => true
    ],
    [
        'name' => 'お得パック', 
        'amount' => 60, 
        'price' => 580, 
        'bonus' => 15, 
        'icon' => '💎'
    ],
];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($assetBase, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>/css/style.css?v=20260424">
    <style>
        /* ショップ専用の追加スタイル */
        .shop-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        .shop-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .shop-title h2 {
            font-size: 24px;
            color: var(--accent);
            margin: 0 0 8px;
        }

        .shop-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .shop-card {
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .shop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.15);
        }

        .shop-card.recommended {
            border: 2px solid var(--accent);
            transform: scale(1.05);
        }

        .shop-card.recommended:hover {
            transform: scale(1.05) translateY(-5px);
        }

        .badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--accent);
            color: #fff;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .plan-icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
        }

        .plan-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .plan-amount {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .plan-bonus {
            font-size: 14px;
            color: #ef4444;
            font-weight: 700;
            margin-bottom: 24px;
            height: 20px;
        }

        .buy-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: var(--accent);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: filter 0.2s;
        }

        .buy-button:hover {
            filter: brightness(1.1);
        }

        @media (max-width: 640px) {
            .shop-card.recommended {
                transform: none;
            }
            .shop-card.recommended:hover {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body class="<?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') ?>">

<header class="page-header">
  <div class="nav-bar">
    <div class="brand-group">
      <div class="brand-title"><?= htmlspecialchars($brandTitle, ENT_QUOTES, 'UTF-8') ?></div>
      <div class="brand-subtitle"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <div style="display: flex; gap: 12px;">
        <button onclick="location.href='gacha.php'" class="logout-button" style="background: rgba(255,255,255,0.1)">戻る</button>
        <button id="logoutButton" type="button" class="logout-button"><?= htmlspecialchars($logoutLabel, ENT_QUOTES, 'UTF-8') ?></button>
    </div>
  </div>
</header>

<main>
    <div class="shop-container">
        <div class="shop-title">
            <h2>コインチャージ</h2>
            <p>ガチャを回すためのコインを購入できます</p>
        </div>

        <div class="shop-grid">
            <?php foreach ($plans as $plan): ?>
                <div class="shop-card <?= isset($plan['recommend']) ? 'recommended' : '' ?>">
                    <?php if (isset($plan['recommend'])): ?>
                        <div class="badge">RECOMMEND</div>
                    <?php endif; ?>
                    
                    <span class="plan-icon"><?= $plan['icon'] ?></span>
                    <div class="plan-name"><?= htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="plan-amount"><?= number_format($plan['amount']) ?><small style="font-size: 14px; margin-left: 4px;">枚</small></div>
                    <div class="plan-bonus">
                        <?= $plan['bonus'] > 0 ? '＋ボーナス ' . number_format($plan['bonus']) . ' 枚' : '' ?>
                    </div>
                    
                    <button class="buy-button" data-amount="<?= ($plan['amount'] + ($plan['bonus'] ?? 0)) ?>" data-name="<?= htmlspecialchars($plan['name'], ENT_QUOTES, 'UTF-8') ?>">
                        ¥<?= number_format($plan['price']) ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.buy-button').forEach(btn => {
    btn.addEventListener('click', async () => {
      const amount = parseInt(btn.dataset.amount || '0', 10);
      const name = btn.dataset.name || '';
      if (amount <= 0) return;
      btn.disabled = true;
      try {
        const res = await fetch('api/coins.php', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'buy', amount })
        });
        const data = await res.json();
        if (res.ok && data.success) {
          alert(name + 'を購入しました。コイン: ' + data.coins + '枚');
          try { localStorage.setItem('coins_update', String(Date.now())); } catch (e) {}
        } else {
          alert(data.message || '購入に失敗しました');
        }
      } catch (e) {
        alert('通信エラー');
      } finally {
        btn.disabled = false;
      }
    });
  });
});
</script>

</body>
</html>