<?php
/**
 * Subcontractor Application API Endpoint
 * Receives form submissions, saves to database, returns JSON.
 */

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Resolve includes/ — local dev uses ../../includes/, live server uses ../includes/
$_includesPath = file_exists(__DIR__ . '/../../includes/db.php')
    ? __DIR__ . '/../../includes/db.php'
    : __DIR__ . '/../includes/db.php';
require_once $_includesPath;

// Rate limiting by IP (max 5 submissions per hour per IP)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$stmt = $db->prepare("
    SELECT COUNT(*) FROM subcontractor_submissions
    WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
");
$stmt->execute([$ip]);
if ((int)$stmt->fetchColumn() >= 5) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many submissions. Please try again later.']);
    exit;
}

// Honeypot — bots fill hidden fields
if (!empty($_POST['website_url'])) {
    // Pretend success but ignore
    echo json_encode(['success' => true]);
    exit;
}

// Collect and sanitize fields
$first_name             = trim($_POST['first_name'] ?? '');
$last_name              = trim($_POST['last_name'] ?? '');
$email                  = trim($_POST['email'] ?? '');
$phone                  = trim($_POST['phone'] ?? '');
$company_name           = trim($_POST['company_name'] ?? '');
$website                = trim($_POST['website'] ?? '');
$years_in_business      = trim($_POST['years_in_business'] ?? '');
$company_size           = trim($_POST['company_size'] ?? '');
$trade                  = trim($_POST['trade'] ?? '');
$trades_other           = trim($_POST['trades_other'] ?? '');
$states_licensed        = trim($_POST['states_licensed'] ?? '');
$service_area           = trim($_POST['service_area'] ?? '');
$license_number         = trim($_POST['license_number'] ?? '');
$insured                = trim($_POST['insured'] ?? '');
$bonded                 = trim($_POST['bonded'] ?? '');
$emr_rating             = trim($_POST['emr_rating'] ?? '');
$union_status           = trim($_POST['union_status'] ?? '');
$project_types_interest = trim($_POST['project_types_interest'] ?? '');
$largest_project        = trim($_POST['largest_project'] ?? '');
$references_text        = trim($_POST['references_text'] ?? '');
$message                = trim($_POST['message'] ?? '');

// Normalise enum-like values
$insured      = in_array($insured, ['yes', 'no', 'in_progress'], true) ? $insured : null;
$bonded       = in_array($bonded, ['yes', 'no'], true) ? $bonded : null;
$union_status = in_array($union_status, ['union', 'non_union', 'either'], true) ? $union_status : null;

// Validate required fields
$errors = [];
if ($first_name === '')   $errors[] = 'First name is required.';
if ($last_name === '')    $errors[] = 'Last name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($phone === '')        $errors[] = 'Phone number is required.';
if ($company_name === '') $errors[] = 'Company name is required.';
if ($trade === '')        $errors[] = 'Trade / specialty is required.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['error' => implode(' ', $errors)]);
    exit;
}

try {
    $stmt = $db->prepare("
        INSERT INTO subcontractor_submissions
        (first_name, last_name, email, phone,
         company_name, website, years_in_business, company_size,
         trade, trades_other, states_licensed, service_area,
         license_number, insured, bonded, emr_rating, union_status,
         project_types_interest, largest_project, references_text, message,
         ip_address, status)
        VALUES
        (?, ?, ?, ?,
         ?, ?, ?, ?,
         ?, ?, ?, ?,
         ?, ?, ?, ?, ?,
         ?, ?, ?, ?,
         ?, 'new')
    ");
    $stmt->execute([
        $first_name, $last_name, $email, $phone,
        $company_name, $website, $years_in_business, $company_size,
        $trade, $trades_other, $states_licensed, $service_area,
        $license_number, $insured, $bonded, $emr_rating, $union_status,
        $project_types_interest, $largest_project, $references_text, $message,
        $ip,
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    error_log('Subcontractor form save failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save your application. Please try again.']);
}
