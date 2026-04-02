<?php
/**
 * Activity Log Helper
 * Logs admin actions to the activity_log table.
 */

/**
 * Log an admin action.
 *
 * @param PDO    $db       Database connection
 * @param string $action   Action type: 'login', 'logout', 'project_create', 'project_edit',
 *                         'project_delete', 'project_publish', 'project_hide',
 *                         'submission_resolve', 'submission_reopen', 'submission_delete',
 *                         'maintenance_on', 'maintenance_off', 'settings_update'
 * @param string $details  Human-readable description
 * @param int|null $userId Override user ID (default: from session)
 */
function logActivity(PDO $db, string $action, string $details, ?int $userId = null): void
{
    $userId = $userId ?? ($_SESSION['user_id'] ?? null);
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    try {
        $stmt = $db->prepare(
            'INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $action, $details, $ip]);
    } catch (PDOException $e) {
        // Silently fail — logging should never break the app
        error_log('Activity log failed: ' . $e->getMessage());
    }
}
