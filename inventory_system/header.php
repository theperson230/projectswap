<?php  
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_name = $user_logged_in ? htmlspecialchars($_SESSION['username']) : 'Guest';
$user_role = $user_logged_in ? $_SESSION['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Procurement System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Procurement System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($user_logged_in): ?>
                        <?php if ($user_role === 'Admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_inventory.php">Manage Inventory</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_vendors.php">Manage Vendors</a></li>
                            <li class="nav-item"><a class="nav-link" href="view_logs.php">View Logs</a></li>
                        <?php elseif ($user_role === 'Procurement Officer'): ?>
                            <li class="nav-item"><a class="nav-link" href="manage_inventory.php">Manage Inventory</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_vendors.php">Manage Vendors</a></li>
                            <li class="nav-item"><a class="nav-link" href="manage_purchase_orders.php">Manage Orders</a></li>
                        <?php elseif ($user_role === 'Department Head'): ?>
                            <li class="nav-item"><a class="nav-link" href="view_procurement_requests.php">View Procurement Requests</a></li>
                        <?php elseif ($user_role === 'Customer' || $user_role === 'User'): ?>
                            <li class="nav-item"><a class="nav-link" href="user_dashboard.php">My Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="read_items.php">View Inventory</a></li>
                            <li class="nav-item"><a class="nav-link" href="read_orders.php">My Orders</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile (<?php echo $user_name; ?>)</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
