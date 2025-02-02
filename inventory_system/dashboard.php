<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Add custom styling -->
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']) . " (" . htmlspecialchars($_SESSION['role']) . ")"; ?></h2>

        <nav>
            <ul>
                <?php if ($_SESSION['role'] == 'Admin'): ?>
                    <li><a href="manage_users.php">Manage Users</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] == 'Procurement Officer'): ?>
                    <li><a href="manage_inventory.php">Manage Inventory</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] == 'Department Head'): ?>
                    <li><a href="view_procurement_requests.php">View Procurement Requests</a></li> <!-- Fixed link -->
                <?php endif; ?>

                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
