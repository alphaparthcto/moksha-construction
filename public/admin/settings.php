<?php
/**
 * Admin Settings — POST handler
 * Handles maintenance_mode toggle and other site settings.
 */

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../includes/db.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/');
    exit;
}

// CSRF check
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['flash_error'] = 'Invalid security token. Please try again.';
    header('Location: /admin/');
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'maintenance_mode') {
        // Toggle maintenance mode: value is "1" when checkbox is checked, absent otherwise
        $value = isset($_POST['maintenance_mode']) ? '1' : '0';

        // Upsert into settings table
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value)
            VALUES ('maintenance_mode', :val)
            ON DUPLICATE KEY UPDATE setting_value = :val2
        ");
        $stmt->execute([':val' => $value, ':val2' => $value]);

        $label = $value === '1' ? 'enabled' : 'disabled';
        $_SESSION['flash_success'] = "Maintenance mode {$label} successfully.";

    } elseif ($action === 'update_settings') {
        // Placeholder for future general settings fields
        // Sanitize and save additional fields here
        $_SESSION['flash_success'] = 'Settings saved.';

    } else {
        $_SESSION['flash_error'] = 'Unknown action.';
    }

} catch (PDOException $e) {
    error_log('Admin settings update failed: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Database error. Please try again.';
}

// Redirect back to dashboard
header('Location: /admin/');
exit;
