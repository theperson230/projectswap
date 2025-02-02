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

    if ($id === false) {
        echo "<div class='alert alert-danger'>Invalid input! ID must be a number.</div>";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $sql = "DELETE FROM inventory WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Log the deletion action
            logAction($_SESSION['username'], "Deleted inventory item ID $id", $conn);
            echo "<div class='alert alert-success'>Inventory item deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting record: " . $stmt->error . "</div>";
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

    <button type="submit" class="btn btn-danger">Delete Item</button>
</form>

<?php include 'footer.php'; ?>
