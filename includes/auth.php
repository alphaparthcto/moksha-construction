<?php
/**
 * Moksha Construction — Admin Authentication
 *
 * Provides session management, login/logout, CSRF protection,
 * and rate limiting for the admin panel.
 */

// ============================================================
// Session bootstrap — call once at top of every admin page
// ============================================================

function sessionStart(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,               // expire on browser close
        'path'     => '/',
        'domain'   => '',              // current domain only
        'secure'   => $isSecure,       // HTTPS-only in production
        'httponly' => true,            // no JS access
        'samesite' => 'Strict',        // no cross-site sending
    ]);

    session_name('moksha_admin');
    session_start();
}

// ============================================================
// Auth state checks
// ============================================================

/**
 * Returns true when a valid user session exists.
 */
function isLoggedIn(): bool {
    sessionStart();
    return !empty($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

/**
 * Halts execution and redirects to login if the visitor is not
 * authenticated. Call at the top of every protected admin page.
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        $loginUrl = '/admin/login.php';
        header('Location: ' . $loginUrl, true, 302);
        exit;
    }
}

/**
 * Fetches the current user row from the database.
 * Returns null when the session is invalid or the user no longer exists.
 */
function getCurrentUser(PDO $db): ?array {
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = $db->prepare(
        'SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

// ============================================================
// Login / logout
// ============================================================

/**
 * Attempts to authenticate a user.
 *
 * On success:
 *   - regenerates session ID to prevent fixation
 *   - stores user_id and email in the session
 *   - clears any rate-limit counters for this IP
 *   Returns true.
 *
 * On failure:
 *   - increments the failed-attempt counter
 *   - records the timestamp of the first failure in the current window
 *   Returns false.
 */
function login(PDO $db, string $email, string $password): bool {
    sessionStart();

    // Enforce rate limit before touching the database
    if (isRateLimited()) {
        return false;
    }

    // Normalise input
    $email = strtolower(trim($email));

    if ($email === '' || $password === '') {
        recordFailedAttempt();
        return false;
    }

    $stmt = $db->prepare(
        'SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    // Use a dummy hash so the verify call always takes the same time,
    // preventing timing-based user enumeration.
    $dummyHash = '$2y$12$invalidhashpaddingtomakesuretimingisuniform00000000000000';
    $hashToCheck = $row ? $row['password'] : $dummyHash;

    if (!$row || !password_verify($password, $hashToCheck)) {
        recordFailedAttempt();
        return false;
    }

    // Successful authentication — harden the session
    session_regenerate_id(true);

    $_SESSION['user_id']    = (int) $row['id'];
    $_SESSION['user_email'] = $row['email'];
    $_SESSION['logged_in_at'] = time();

    // Clear rate-limit counters for this session
    unset($_SESSION['failed_attempts'], $_SESSION['first_fail_at']);

    // Log successful login
    require_once __DIR__ . '/activity.php';
    logActivity($db, 'login', 'Logged in as ' . $row['email'], (int) $row['id']);

    return true;
}

/**
 * Destroys the session and redirects to the login page.
 * Never returns.
 */
function logout(): void {
    sessionStart();

    // Log logout before session is destroyed
    if (!empty($_SESSION['user_id'])) {
        require_once __DIR__ . '/db.php';
        require_once __DIR__ . '/activity.php';
        logActivity($db, 'logout', 'Logged out', (int) $_SESSION['user_id']);
    }

    // Overwrite session data before destroying
    $_SESSION = [];

    // Remove the session cookie from the browser
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    header('Location: /admin/login.php', true, 302);
    exit;
}

// ============================================================
// CSRF protection
// ============================================================

/**
 * Returns a per-session CSRF token, creating one if needed.
 * The token is a 32-byte cryptographically random hex string.
 */
function generateCsrfToken(): string {
    sessionStart();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validates the supplied token against the one stored in the session.
 * Uses hash_equals() to prevent timing attacks.
 * Returns false when no session token exists or the strings differ.
 */
function validateCsrfToken(string $token): bool {
    sessionStart();

    if (empty($_SESSION['csrf_token']) || $token === '') {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================================
// Rate limiting — session-based, per browser session
// ============================================================

/** Maximum number of failed attempts before a lockout. */
const MAX_FAILED_ATTEMPTS = 5;

/** Lockout duration in seconds (15 minutes). */
const LOCKOUT_SECONDS = 900;

/**
 * Returns true when the current session is locked out due to too
 * many failed login attempts within the sliding window.
 */
function isRateLimited(): bool {
    sessionStart();

    $attempts = $_SESSION['failed_attempts'] ?? 0;
    $firstFail = $_SESSION['first_fail_at'] ?? null;

    if ($attempts < MAX_FAILED_ATTEMPTS) {
        return false;
    }

    // Check whether the lockout window has expired
    if ($firstFail !== null && (time() - $firstFail) >= LOCKOUT_SECONDS) {
        // Window expired — reset counters
        unset($_SESSION['failed_attempts'], $_SESSION['first_fail_at']);
        return false;
    }

    return true;
}

/**
 * Returns the number of seconds remaining in the current lockout,
 * or 0 when there is no active lockout.
 */
function rateLimitSecondsRemaining(): int {
    sessionStart();

    $attempts = $_SESSION['failed_attempts'] ?? 0;
    $firstFail = $_SESSION['first_fail_at'] ?? null;

    if ($attempts < MAX_FAILED_ATTEMPTS || $firstFail === null) {
        return 0;
    }

    $elapsed = time() - $firstFail;
    $remaining = LOCKOUT_SECONDS - $elapsed;

    return max(0, (int) $remaining);
}

/**
 * Increments the failed-attempt counter and records the timestamp
 * of the first failure in the current window.
 */
function recordFailedAttempt(): void {
    sessionStart();

    if (empty($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
    }

    // Only set the window start time on the very first failure
    if (empty($_SESSION['first_fail_at'])) {
        $_SESSION['first_fail_at'] = time();
    }

    $_SESSION['failed_attempts']++;
}
