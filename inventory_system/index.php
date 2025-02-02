<?php
session_start();
include 'header.php'; 
?>

<div class="container mt-5">
    <h2 class="text-center">Welcome to the Procurement System</h2>
    <p class="text-center">Manage inventory, purchase items, and track orders easily.</p>

    <div class="text-center">
        <?php if (!isset($_SESSION['user_id'])) { ?>
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
        <?php } else { ?>
            <a href="purchase.php" class="btn btn-success">Purchase Items</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
