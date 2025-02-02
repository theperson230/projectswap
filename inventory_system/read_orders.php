<?php 
session_start();
include 'header.php'; 
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=unauthorized_access");
    exit();
}

// Fetch orders securely
$stmt = $conn->prepare("SELECT id, item_name, sku, quantity, status FROM purchase_orders");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h2 class="text-center">My Purchase Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["id"]); ?></td>
                        <td><?php echo htmlspecialchars($row["item_name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["sku"]); ?></td>
                        <td><?php echo htmlspecialchars($row["quantity"]); ?></td>
                        <td><?php echo htmlspecialchars($row["status"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning text-center">No purchase orders found.</p>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
$conn->close();
include 'footer.php'; 
?>
