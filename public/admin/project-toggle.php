<?php
/**
 * Moksha Construction Admin — Project Status Toggle (POST only)
 *
 * Accepts a project ID and a new status value, validates both, and
 * updates the projects table. Redirects back to the dashboard.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAuth();

// ---------------------------------------------------------------------------
// POST only
// ---------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

$token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($token)) {
    http_response_code(403);
    exit('Invalid CSRF token. Please go back and try again.');
}

// ---------------------------------------------------------------------------
// Validate inputs
// ---------------------------------------------------------------------------

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = trim($_POST['status'] ?? '');

$allowedStatuses = ['published', 'draft', 'hidden'];

if ($id < 1) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Invalid project ID.',
    ];
    header('Location: /admin/');
    exit;
}

if (!in_array($status, $allowedStatuses, true)) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Invalid status value.',
    ];
    header('Location: /admin/');
    exit;
}

// ---------------------------------------------------------------------------
// Update project status
// ---------------------------------------------------------------------------

try {
    $stmt = $db->prepare('UPDATE projects SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);

    if ($stmt->rowCount() === 0) {
        $_SESSION['flash'] = [
            'type'    => 'error',
            'message' => 'Project not found.',
        ];
        header('Location: /admin/');
        exit;
    }
} catch (PDOException $e) {
    error_log('Project toggle DB error: ' . $e->getMessage());

    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'A database error occurred. Status was not updated.',
    ];
    header('Location: /admin/');
    exit;
}

// ---------------------------------------------------------------------------
// Redirect with success flash
// ---------------------------------------------------------------------------

$statusLabel = ucfirst($status);

$_SESSION['flash'] = [
    'type'    => 'success',
    'message' => 'Project status changed to ' . $statusLabel . '.',
];

header('Location: /admin/');
exit;
