<?php
// Start session only if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure this file is only included once
if (!defined('DB_CONNECTIONS_INCLUDED')) {
    define('DB_CONNECTIONS_INCLUDED', true);

    // Database connection
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "procurement_database"; // Change to your real DB name

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Role-based permissions
    if (!function_exists('canCreateVendor')) {
        function canCreateVendor() {
            return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'procurement_officer']);
        }
    }

    if (!function_exists('canReadVendor')) {
        function canReadVendor() {
            return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'procurement_officer', 'department_head']);
        }
    }

    if (!function_exists('canUpdateVendor')) {
        function canUpdateVendor() {
            return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'procurement_officer']);
        }
    }

    if (!function_exists('canDeleteVendor')) {
        function canDeleteVendor() {
            return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        }
    }
}
?>
