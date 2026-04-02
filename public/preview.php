<?php
$key = $_GET['key'] ?? '';
if ($key === 'moksha2026') {
    setcookie('moksha_preview', '1', time() + 86400, '/', '.moksha.construction'); // 24 hours, all subdomains
    header('Location: https://moksha.construction/');
    exit;
}
// Wrong key or no key — just go to maintenance
header('Location: /maintenance.html');
exit;
