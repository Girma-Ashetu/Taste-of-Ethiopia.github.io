<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Protect pages based on role
 *
 * @param string $requiredRole  'admin', 'customer', or 'any'
 * @param bool $showAlert       true = show JS alert if guest clicks
 */
function protectPage($requiredRole = 'any', $showAlert = false)
{

    if (!isset($_SESSION['role'])) {
        // Guest clicked a protected page
        if ($showAlert) {
            $_SESSION['guest_alert'] = "⚠️ Please login first to access this page!";
        }
        header("Location: login.php");
        exit;
    }

    $role = $_SESSION['role'];

    if ($requiredRole === 'admin' && $role !== 'admin') {
        die("🚫 Access denied. Admins only.");
    }

    if ($requiredRole === 'customer' && $role !== 'customer' && $role !== 'admin') {
        die("🚫 Access denied. Customers only.");
    }
}
