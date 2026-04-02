<?php
/**
 * Contact Form API Endpoint
 * Receives form submissions, saves to database, returns JSON.
 */

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Rate limiting by IP (simple: max 5 submissions per hour per IP)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$stmt = $db->prepare("
    SELECT COUNT(*) FROM contact_submissions
    WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
");
$stmt->execute([$ip]);
if ((int)$stmt->fetchColumn() >= 5) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many submissions. Please try again later.']);
    exit;
}

// Collect and sanitize fields
$first_name   = trim($_POST['first_name'] ?? '');
$last_name    = trim($_POST['last_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$company      = trim($_POST['company'] ?? '');
$project_type = trim($_POST['project_type'] ?? '');
$location     = trim($_POST['location'] ?? '');
$budget       = trim($_POST['budget'] ?? '');
$timeline     = trim($_POST['timeline'] ?? '');
$message      = trim($_POST['message'] ?? '');

// Validate required fields
$errors = [];
if ($first_name === '') $errors[] = 'First name is required.';
if ($last_name === '')  $errors[] = 'Last name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($phone === '')      $errors[] = 'Phone number is required.';
if ($project_type === '') $errors[] = 'Project type is required.';
if ($message === '')    $errors[] = 'Message is required.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['error' => implode(' ', $errors)]);
    exit;
}

try {
    $stmt = $db->prepare("
        INSERT INTO contact_submissions
        (first_name, last_name, email, phone, company, project_type, location, budget, timeline, message, ip_address, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')
    ");
    $stmt->execute([
        $first_name, $last_name, $email, $phone, $company,
        $project_type, $location, $budget, $timeline, $message, $ip
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    error_log('Contact form save failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save your submission. Please try again.']);
}
