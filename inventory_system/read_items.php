<?php 
session_start();
include 'header.php'; 
include 'db_connect.php';

// Ensure user is logged in and has the correct role
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'User' && $_SESSION['role'] !== 'Customer')) {
    header("Location: login.php?error=unauthorized_access");
    exit();
}

// Fetch inventory data securely
$stmt = $conn->prepare("SELECT id, product_name, sku, quantity, restock_level FROM inventory");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h2 class="text-center">Available Inventory</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Restock Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["id"]); ?></td>
                        <td><?php echo htmlspecialchars($row["product_name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["sku"]); ?></td>
                        <td><?php echo htmlspecialchars($row["quantity"]); ?></td>
                        <td><?php echo htmlspecialchars($row["restock_level"]); ?></td>
                        <td>
                            <form action="create_order.php" method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $row["id"]; ?>">
                                <input type="number" name="quantity" min="1" max="<?php echo $row["quantity"]; ?>" required class="form-control mb-2">
                                <button type="submit" class="btn btn-success">Purchase</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning text-center">No inventory items found.</p>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
$conn->close();
include 'footer.php'; 
?>
