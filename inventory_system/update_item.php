<?php 
include 'header.php';  
include 'db_connect.php'; // No need to start session again

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Sanitize input
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $restock_level = filter_var($_POST['restock_level'], FILTER_VALIDATE_INT);

    if ($id === false || $quantity === false || $restock_level === false) {
        echo "<div class='alert alert-danger'>Invalid input! ID, Quantity, and Restock Level must be numbers.</div>";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $sql = "UPDATE inventory SET quantity=?, restock_level=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $restock_level, $id);

        if ($stmt->execute()) {
            logAction($_SESSION['username'], "Updated inventory item ID $id with quantity $quantity and restock level $restock_level", $conn);
            echo "<div class='alert alert-success'>Inventory item updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating record: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="mb-3">
        <label class="form-label">Item ID:</label>
        <input type="number" name="id" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">New Quantity:</label>
        <input type="number" name="quantity" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">New Restock Level:</label>
        <input type="number" name="restock_level" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-warning">Update Item</button>
</form>

<?php include 'footer.php'; ?>
