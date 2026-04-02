<?php
/**
 * Moksha Construction — Site Configuration
 */

define('SITE_NAME', 'Moksha Construction');
define('SITE_URL', 'https://moksha.construction');
define('SITE_PHONE', '+1 (615) 234-0272');
define('SITE_PHONE_RAW', '+16152340272');
define('SITE_EMAIL', 'info@moksha.construction');
define('SITE_YEAR', date('Y'));

// Office locations
define('OFFICE_NASHVILLE', [
    'name' => 'Nashville Office',
    'street' => '315 Deaderick Street, Suite 1550',
    'city' => 'Nashville',
    'state' => 'TN',
    'zip' => '37238',
    'country' => 'US',
]);

define('OFFICE_ATLANTA', [
    'name' => 'Atlanta Office',
    'street' => '1 W Court Square',
    'city' => 'Decatur',
    'state' => 'GA',
    'zip' => '30030',
    'country' => 'US',
]);

// Social links (update with real URLs from client)
define('SOCIAL_INSTAGRAM', 'https://instagram.com/mokshaconstruction');
define('SOCIAL_FACEBOOK', 'https://facebook.com/mokshaconstruction');
define('SOCIAL_LINKEDIN', 'https://linkedin.com/company/moksha-construction');

// Google Apps Script form endpoint (update with real URL)
define('FORM_ENDPOINT', 'https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec');

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'parths5_moksha');
define('DB_USER', 'parths5_moksha');
define('DB_PASS', '95GAsiK4ghpaMoJ8oHql');

// Asset versioning for cache busting
define('ASSET_VERSION', '1.0.3');
