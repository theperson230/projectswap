<?php
session_start();
include 'db_connect.php';

// Ensure session is active and user has the correct role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'User' && $_SESSION['role'] !== 'Customer')) {
    header("Location: login.php?error=unauthorized_access");
    exit();
}

?>

<?php include 'header.php'; ?>
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="col-md-6">
        <div class="card shadow p-4">
            <h2 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p class="text-center">You can browse available inventory, purchase items, and check your past orders.</p>

            <div class="d-grid gap-3">
                <a href="read_items.php" class="btn btn-primary btn-lg">View Available Items</a>
                <a href="create_order.php" class="btn btn-success btn-lg">Purchase Items</a>
                <a href="read_orders.php" class="btn btn-secondary btn-lg">View My Orders</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
