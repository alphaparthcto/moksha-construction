<?php
/**
 * Moksha Construction — Admin Logout
 * Destroys the session and redirects to the login page.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// requireAuth() first so unauthenticated GET /admin/logout.php
// still lands on the login page rather than triggering an error.
requireAuth();

logout(); // destroys session and redirects — never returns
