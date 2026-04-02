<?php
/**
 * Moksha Construction — Admin Login
 * Standalone page: no header.php / footer.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

sessionStart();

// Already logged in — skip straight to dashboard
if (isLoggedIn()) {
    header('Location: /admin/', true, 302);
    exit;
}

$error         = '';
$rateLimited   = false;
$secondsLeft   = 0;

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check first — fail fast before touching credentials
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($submittedToken)) {
        $error = 'Invalid form submission. Please refresh the page and try again.';
    } elseif (isRateLimited()) {
        $rateLimited = true;
        $secondsLeft = rateLimitSecondsRemaining();
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (login($db, $email, $password)) {
            // Rotate CSRF token after successful login
            unset($_SESSION['csrf_token']);

            header('Location: /admin/', true, 302);
            exit;
        } else {
            if (isRateLimited()) {
                $rateLimited = true;
                $secondsLeft = rateLimitSecondsRemaining();
            } else {
                $attemptsLeft = MAX_FAILED_ATTEMPTS - ($_SESSION['failed_attempts'] ?? 0);
                $error = 'Invalid email or password.';
                if ($attemptsLeft <= 2) {
                    $error .= ' ' . $attemptsLeft . ' attempt' . ($attemptsLeft === 1 ? '' : 's') . ' remaining before lockout.';
                }
            }
        }
    }
}

// Generate (or reuse) CSRF token for the form
$csrfToken = generateCsrfToken();

// Safely echo user-supplied values back into the form
$emailValue = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Moksha Construction</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ── Reset & base ───────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --void:       #0d0510;
      --base:       #100d14;
      --surface:    #18141e;
      --raised:     #211c29;
      --overlay:    #2c2538;
      --border:     rgba(255,255,255,0.07);
      --border-focus: #FFE907;

      --purple:     #9517B3;
      --purple-dim: rgba(149,23,179,0.18);
      --gold:       #FFE907;
      --gold-dim:   rgba(255,233,7,0.12);
      --gold-dark:  #c4b300;

      --text:       rgba(255,255,255,0.93);
      --text-2:     rgba(255,255,255,0.60);
      --text-3:     rgba(255,255,255,0.35);

      --radius-sm:  6px;
      --radius-md:  12px;
      --radius-lg:  20px;

      --shadow-card: 0 0 60px rgba(149,23,179,0.15), 0 24px 48px rgba(0,0,0,0.5);
      --shadow-gold: 0 0 30px rgba(255,233,7,0.20);
    }

    html {
      height: 100%;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    body {
      min-height: 100%;
      background-color: var(--void);
      color: var(--text);
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      font-size: 1rem;
      line-height: 1.6;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow-x: hidden;
    }

    /* ── Background glow orbs ───────────────────────────────── */
    body::before,
    body::after {
      content: '';
      position: fixed;
      border-radius: 50%;
      pointer-events: none;
      filter: blur(80px);
      z-index: 0;
    }

    body::before {
      width: 600px;
      height: 600px;
      top: -200px;
      right: -150px;
      background: radial-gradient(circle, rgba(149,23,179,0.12) 0%, transparent 70%);
    }

    body::after {
      width: 500px;
      height: 500px;
      bottom: -200px;
      left: -150px;
      background: radial-gradient(circle, rgba(255,233,7,0.06) 0%, transparent 70%);
    }

    /* ── Card ───────────────────────────────────────────────── */
    .login-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 420px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 2.75rem 2.5rem;
      box-shadow: var(--shadow-card);
    }

    /* Gold top accent line */
    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 2.5rem;
      right: 2.5rem;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--gold), transparent);
      border-radius: 0 0 2px 2px;
    }

    /* ── Logo area ──────────────────────────────────────────── */
    .logo-wrap {
      display: flex;
      justify-content: center;
      margin-bottom: 2rem;
    }

    .logo-wrap img {
      height: 72px;
      width: auto;
      object-fit: contain;
    }

    /* ── Heading ────────────────────────────────────────────── */
    .login-heading {
      font-size: 1.375rem;
      font-weight: 700;
      color: var(--text);
      text-align: center;
      letter-spacing: -0.02em;
      margin-bottom: 0.375rem;
    }

    .login-sub {
      font-size: 0.8125rem;
      color: var(--text-3);
      text-align: center;
      margin-bottom: 2rem;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    /* ── Alert banner ───────────────────────────────────────── */
    .alert {
      border-radius: var(--radius-sm);
      padding: 0.875rem 1rem;
      font-size: 0.875rem;
      line-height: 1.5;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: flex-start;
      gap: 0.625rem;
    }

    .alert-error {
      background: rgba(220, 38, 38, 0.12);
      border: 1px solid rgba(220, 38, 38, 0.28);
      color: #fca5a5;
    }

    .alert-warn {
      background: rgba(234, 179, 8, 0.10);
      border: 1px solid rgba(234, 179, 8, 0.28);
      color: #fde047;
    }

    .alert-icon {
      flex-shrink: 0;
      width: 16px;
      height: 16px;
      margin-top: 2px;
    }

    /* ── Form elements ──────────────────────────────────────── */
    .field {
      margin-bottom: 1.25rem;
    }

    .field label {
      display: block;
      font-size: 0.8125rem;
      font-weight: 500;
      color: var(--text-2);
      margin-bottom: 0.5rem;
      letter-spacing: 0.01em;
    }

    .field input {
      width: 100%;
      padding: 0.8125rem 1rem;
      background: var(--raised);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font-family: inherit;
      font-size: 0.9375rem;
      line-height: 1;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
      -webkit-appearance: none;
    }

    .field input::placeholder {
      color: var(--text-3);
    }

    .field input:focus {
      border-color: var(--gold);
      background: var(--overlay);
      box-shadow: 0 0 0 3px rgba(255,233,7,0.10);
    }

    /* Disabled state during lockout */
    .field input:disabled {
      opacity: 0.45;
      cursor: not-allowed;
    }

    /* ── Submit button ──────────────────────────────────────── */
    .btn-login {
      width: 100%;
      padding: 0.9375rem 1rem;
      margin-top: 0.5rem;
      background: var(--gold);
      color: #1a1500;
      font-family: inherit;
      font-size: 0.9375rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      border: none;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: background 0.2s, transform 0.2s cubic-bezier(0.16,1,0.3,1), box-shadow 0.2s;
    }

    .btn-login:hover:not(:disabled) {
      background: #fff176;
      transform: translateY(-1px);
      box-shadow: var(--shadow-gold);
    }

    .btn-login:active:not(:disabled) {
      transform: translateY(0);
      box-shadow: none;
    }

    .btn-login:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }

    /* ── Footer note ────────────────────────────────────────── */
    .card-footer {
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border);
      text-align: center;
      font-size: 0.75rem;
      color: var(--text-3);
    }

    .card-footer a {
      color: var(--text-3);
      text-decoration: none;
    }

    .card-footer a:hover {
      color: var(--text-2);
    }

    /* ── Countdown ──────────────────────────────────────────── */
    #countdown {
      font-weight: 700;
      color: #fde047;
    }

    /* ── Reduced motion ─────────────────────────────────────── */
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { transition-duration: 0.01ms !important; }
    }
  </style>
</head>
<body>

  <div class="login-card">

    <!-- Logo -->
    <div class="logo-wrap">
      <img src="/assets/images/branding/logo-full-color-vertical.png"
           alt="Moksha Construction"
           onerror="this.style.display='none'">
    </div>

    <h1 class="login-heading">Admin Login</h1>
    <p class="login-sub">Moksha Construction</p>

    <?php if ($rateLimited): ?>
      <!-- Rate-limit lockout banner -->
      <div class="alert alert-warn" role="alert">
        <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
        </svg>
        <span>
          Too many failed attempts. Please wait
          <span id="countdown"><?= htmlspecialchars((string)$secondsLeft, ENT_QUOTES, 'UTF-8') ?></span>
          seconds before trying again.
        </span>
      </div>
    <?php elseif ($error !== ''): ?>
      <!-- Credential error banner -->
      <div class="alert alert-error" role="alert">
        <svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
        </svg>
        <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    <?php endif; ?>

    <!-- Login form -->
    <form method="POST" action="/admin/login.php" novalidate autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

      <div class="field">
        <label for="email">Email address</label>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= $emailValue ?>"
          placeholder="you@example.com"
          autocomplete="email"
          required
          <?= $rateLimited ? 'disabled' : '' ?>
        >
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="••••••••"
          autocomplete="current-password"
          required
          <?= $rateLimited ? 'disabled' : '' ?>
        >
      </div>

      <button
        type="submit"
        class="btn-login"
        <?= $rateLimited ? 'disabled' : '' ?>
      >Sign in</button>
    </form>

    <div class="card-footer">
      <a href="<?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?>">&larr; Back to site</a>
    </div>

  </div><!-- /.login-card -->

  <?php if ($rateLimited && $secondsLeft > 0): ?>
  <script>
    (function () {
      var el = document.getElementById('countdown');
      if (!el) return;
      var remaining = <?= (int)$secondsLeft ?>;
      var tick = setInterval(function () {
        remaining--;
        if (remaining <= 0) {
          clearInterval(tick);
          window.location.reload();
        } else {
          el.textContent = remaining;
        }
      }, 1000);
    })();
  </script>
  <?php endif; ?>

</body>
</html>
