<?php
session_start();
require 'db_connections.php';

// Restrict access: Only Admins and Procurement Officers can access admin.php
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'procurement_officer'])) {
    header("Location: headDEP.php"); // Redirect Department Heads to their page
    exit();
}

// Define role-based permissions
function canCreateVendor() {
    return in_array($_SESSION['role'], ['admin', 'procurement_officer']);
}

function canReadVendor() {
    return in_array($_SESSION['role'], ['admin', 'procurement_officer', 'department_head']);
}

function canUpdateVendor() {
    return in_array($_SESSION['role'], ['admin', 'procurement_officer']);
}

function canDeleteVendor() {
    return $_SESSION['role'] === 'admin';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>

<div class="header">
    <h1>Dashboard</h1>
</div>
<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['role']); ?>!</h2>

    <div class="card-grid">
        <!-- Create Vendor (Admin & Officer only) -->
        <?php if (canCreateVendor()): ?>
        <div class="card">
            <h3>Create Vendor</h3>
            <a class="go-btn" href="create_vendor_profile.php">Go</a>
        </div>
        <?php endif; ?>

        <!-- Read Vendors (All 3 roles) -->
        <?php if (canReadVendor()): ?>
        <div class="card">
            <h3>View Vendors</h3>
            <a class="go-btn" href="read_records.php">Go</a>
        </div>
        <?php endif; ?>
        <?php  ?>
        <div class="card">
            <h3>View Orders</h3>
            <a class="go-btn" href="http://localhost/proj1/insertupdatedelete.php?role=admin">Go</a>
        </div>
        <?php  ?>
    </div>

    <a class="logout-btn" href="login.php">Logout</a>
</div>

</body>
</html>
