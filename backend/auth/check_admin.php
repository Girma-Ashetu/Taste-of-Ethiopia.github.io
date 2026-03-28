<?php
// auth/check_admin.php

// Start session ONLY if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Not logged in → redirect to login page
    header("Location: login.php");
    exit;
}

// (Optional) Regenerate session ID for security
// This helps prevent session fixation attacks
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}
