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
    $contact = htmlspecialchars(strip_tags($_POST['contact']));
    $services = htmlspecialchars(strip_tags($_POST['services']));

    if ($id === false || empty($contact) || empty($services)) {
        echo "<div class='alert alert-danger'>Invalid input!</div>";
    } else {
        // Prepare SQL statement to prevent SQL injection
        $sql = "UPDATE vendors SET contact=?, services=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $contact, $services, $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Vendor updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating vendor: " . $stmt->error . "</div>";
        }
        $stmt->close();
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

    <div class="mb-3">
        <label class="form-label">New Contact Info:</label>
        <input type="text" name="contact" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">New Services:</label>
        <input type="text" name="services" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-warning">Update Vendor</button>
</form>

<?php include 'footer.php'; ?>
