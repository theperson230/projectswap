<?php
session_start();
include 'header.php'; // Include navigation
include 'db.php'; // Connect to database

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo '<div class="alert alert-danger text-center">Access Denied. Please <a href="login.php">Login</a></div>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Define query based on role
if ($user_role == 'Admin' || $user_role == 'Procurement Officer') {
    // Admins and Procurement Officers can view all purchase orders
    $query = "SELECT p.id, v.vendor_name, i.product_name, p.quantity, p.status 
              FROM purchase_orders p
              JOIN vendors v ON p.vendor_id = v.id
              JOIN inventory i ON p.item_id = i.id";
} else {
    // Department Heads can only view their department’s records
    $query = "SELECT p.id, v.vendor_name, i.product_name, p.quantity, p.status 
              FROM purchase_orders p
              JOIN vendors v ON p.vendor_id = v.id
              JOIN inventory i ON p.item_id = i.id
              WHERE p.requested_by = '$user_id'";
}

$result = $conn->query($query);
?>

<div class="container">
    <h2 class="text-center">Procurement Records</h2>
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
