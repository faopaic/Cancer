<?php

declare(strict_types=1);

$pageTitle = 'ガチャ';
$bodyClass = '';
$scriptFile = 'script';
$assetBase = 'assets';
$brandTitle = 'Cancer Gacha';
$subtitle = 'ようこそ、プレイヤーさん';
$logoutLabel = 'ログアウト';
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($assetBase, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>/css/style.css?v=20260424">
  </head>
  <body<?= $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"' : '' ?>>

<header class="page-header">
  <div class="nav-bar">
    <div class="brand-group">
      <div class="brand-title"><?= htmlspecialchars($brandTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></div>
      <div class="brand-subtitle" id="userGreeting"><?= htmlspecialchars($subtitle, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></div>
    </div>
    <nav class="nav-actions">
      <a href="shop.php" class="shop-button" role="button">ショップ</a>
      <div id="coinDisplay" class="coin-display" aria-live="polite" style="color: #fff; font-weight:700;">コイン: —</div>
      <button id="logoutButton" type="button" class="logout-button"><?= htmlspecialchars($logoutLabel, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?></button>
    </nav>
  </div>
</header>

<main>
  <section class="gacha-page">
    <div class="gacha-panel">
      <div class="gacha-card">
        <img src="photo/gacha.png" alt="ガチャ画像" class="gacha-image">
        <div class="handle" id="handle" aria-label="ガチャハンドル">
          <div class="handle-inner"></div>
        </div>
      </div>
      <div class="gacha-footer">
        <div class="gacha-message" id="gachaMessage">ハンドルを回してガチャを引こう！</div>
      </div>
    </div>
  </section>
</main>

<?php if ($scriptFile !== ''): ?>
    <script src="<?= htmlspecialchars($assetBase, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>/js/<?= htmlspecialchars($scriptFile, ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>.js?v=20260424"></script>
<?php endif; ?>
  </body>
</html>
