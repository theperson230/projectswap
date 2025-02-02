<?php 
include 'header.php';  
include 'db_connect.php'; // No need to start session again

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Validate input
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    // Check if order is still "Pending"
    $check_sql = "SELECT status FROM purchase_orders WHERE id=?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order && $order['status'] === 'Pending') {
        // Delete the order
        $sql = "DELETE FROM purchase_orders WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Purchase order deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting order: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Only 'Pending' orders can be deleted.</div>";
    }
}
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="mb-3">
        <label class="form-label">Order ID:</label>
        <input type="number" name="id" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-danger">Delete Order</button>
</form>

<?php include 'footer.php'; ?>
