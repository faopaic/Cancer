const gachaMessage = document.getElementById('gachaMessage');
const handle = document.getElementById('handle');
const logoutButton = document.getElementById('logoutButton');
const userGreeting = document.getElementById('userGreeting');

let currentUsername = '';
let currentCoins = 0;

const fetchCoins = async () => {
  try {
    const res = await fetch('api/coins.php', { method: 'GET', credentials: 'same-origin' });
    if (!res.ok) return;
    const data = await res.json();
    if (data.success) {
      currentCoins = Number(data.coins) || 0;
      const coinEl = document.getElementById('coinDisplay');
      if (coinEl) coinEl.textContent = `コイン: ${currentCoins}`;
    }
  } catch (e) {
    // ignore
  }
};

window.addEventListener('storage', (e) => {
  if (e.key === 'coins_update') {
    fetchCoins();
  }
});

const ensureAuthenticated = async () => {
  try {
    const response = await fetch('api/me.php', {
      method: 'GET',
      credentials: 'same-origin',
    });
    if (!response.ok) {
      window.location.href = 'index.php';
      return false;
    }

    const data = await response.json();
    if (!data.success) {
      window.location.href = 'index.php';
      return false;
    }

    currentUsername = data.user.username ?? '';
    if (userGreeting) {
      userGreeting.textContent = `ようこそ、${currentUsername} さん`;
    }

    // me に coins を追加したので取得して表示
    if (typeof data.user.coins !== 'undefined') {
      currentCoins = Number(data.user.coins) || 0;
      const coinEl = document.getElementById('coinDisplay');
      if (coinEl) coinEl.textContent = `コイン: ${currentCoins}`;
    } else {
      await fetchCoins();
    }

    return true;
  } catch (error) {
    window.location.href = 'index.php';
    return false;
  }
};

const performLogout = async () => {
  try {
    await fetch('api/logout.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({}),
    });
  } catch (error) {
    // ignore errors and redirect anyway
  } finally {
    window.location.href = 'index.php';
  }
};

if (logoutButton) {
  logoutButton.addEventListener('click', performLogout);
}

const initGacha = async () => {
  const isAuthenticated = await ensureAuthenticated();
  if (!isAuthenticated || !handle) {
    return;
  }

  let isPointerDown = false;
  let lastAngle = 0;
  let currentRotation = 0;
  let lastSpinTime = 0;
  let spinAmount = 0; // 回した量を記録

  const getAngle = (event) => {
    const rect = handle.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    const x = event.clientX - centerX;
    const y = event.clientY - centerY;
    return Math.atan2(y, x);
  };

  const finishSpin = async () => {
    const now = Date.now();
    if (now - lastSpinTime < 500) return;
    lastSpinTime = now;

    // ガチャ1回につき10コイン消費
    try {
      const res = await fetch('api/coins.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'spend', amount: 10 })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        if (gachaMessage) gachaMessage.textContent = data.message || 'コインが足りません。';
        return;
      }

      currentCoins = Number(data.coins) || 0;
      const coinEl = document.getElementById('coinDisplay');
      if (coinEl) coinEl.textContent = `コイン: ${currentCoins}`;

      // ブロードキャストして他タブの表示も更新
      try { localStorage.setItem('coins_update', String(Date.now())); } catch (e) {}

      if (gachaMessage) {
        gachaMessage.textContent = 'ガチャを回しました！結果を確認しましょう。';
      }
    } catch (e) {
      if (gachaMessage) gachaMessage.textContent = 'エラーが発生しました。';
    }
  };

  handle.addEventListener('pointerdown', async (event) => {
    event.preventDefault();

    // 先に最新残高を取得してチェック
    await fetchCoins();
    if (currentCoins < 10) {
      if (gachaMessage) gachaMessage.textContent = 'コインが足りません。ショップで購入してください。';
      return;
    }

    isPointerDown = true;
    handle.setPointerCapture(event.pointerId);
    lastAngle = getAngle(event);
  });

  handle.addEventListener('pointermove', (event) => {
    if (!isPointerDown) return;

    const angle = getAngle(event);
    let delta = angle - lastAngle;

    // 角度補正
    if (delta > Math.PI) delta -= 2 * Math.PI;
    if (delta < -Math.PI) delta += 2 * Math.PI;

    // 左回り（マイナス）は無視
    if (delta < 0) delta = 0;

    currentRotation += delta;
    spinAmount += delta; // 回転量を加算
    lastAngle = angle;

    handle.style.transform = `rotate(${currentRotation}rad)`;
  });

  handle.addEventListener('pointerup', () => {
    if (isPointerDown) {
      // 半回転以上でガチャ発動（πラジアン）
      if (spinAmount >= Math.PI) {
        finishSpin();
      } else {
        if (gachaMessage) {
          gachaMessage.textContent = 'もっと回してください！';
        }
      }
    }

    isPointerDown = false;
    spinAmount = 0; // リセット
  });
};

initGacha();

