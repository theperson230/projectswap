<?php
session_start();
require 'db_connections.php';

// Ensure only Department Heads can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'department_head') {
    die("Unauthorized access.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard</title>
    <link rel="stylesheet" type="text/css" href="headDEP.css">
</head>
<body>

<div class="header">
    <h1>Dashboard</h1>
</div>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['role']); ?>!</h2>

    <div class="card-grid">
        <div class="card">
            <h3>View Vendors</h3>
            <a class="go-btn" href="read_records.php">Go</a>
        </div>
    </div>

    <a class="logout-btn" href="login.php">Logout</a>
</div>

</body>
</html>
