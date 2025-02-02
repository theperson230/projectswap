<?php 
include 'header.php';  
include 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Validate input
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

    if ($id === false) {
        echo "<div class='alert alert-danger'>Invalid input! Vendor ID must be a number.</div>";
    } else {
        // Check if vendor exists
        $check_sql = "SELECT id FROM vendors WHERE id=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // Delete vendor
            $sql = "DELETE FROM vendors WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Vendor deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error deleting vendor: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-warning'>Vendor ID does not exist.</div>";
        }
    }
}
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="mb-3">
        <label class="form-label">Vendor ID:</label>
        <input type="number" name="id" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-danger">Delete Vendor</button>
</form>

<?php include 'footer.php'; ?>
