<?php
include 'header.php';
include 'db_connect.php'; // No need to start session again

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Sanitize input
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    $status = htmlspecialchars($_POST['status']); // Prevent XSS

    if ($order_id === false || empty($status)) {
        echo "<div class='alert alert-danger'>Invalid input! Order ID must be a number and status must be provided.</div>";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $sql = "UPDATE purchase_orders SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            // Log the update action
            logAction($_SESSION['username'], "Updated order ID $order_id to status $status", $conn);
            echo "<div class='alert alert-success'>Order status updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating order: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="mb-3">
        <label class="form-label">Order ID:</label>
        <input type="number" name="order_id" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">New Status:</label>
        <select name="status" class="form-control" required>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Completed">Completed</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update Order</button>
</form>

<?php include 'footer.php'; ?>
