<?php 
include 'header.php';  
include 'db_connect.php'; // No need to start session again

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Sanitize input
    $name = htmlspecialchars(strip_tags($_POST['name']));
    $contact = htmlspecialchars(strip_tags($_POST['contact']));
    $services = htmlspecialchars(strip_tags($_POST['services']));
    $payment_details = openssl_encrypt($_POST['payment_details'], "AES-128-CTR", "your_secret_key", 0, "1234567891011121"); // Encrypt payment details

    if (!empty($name) && !empty($contact) && !empty($services) && !empty($_POST['payment_details'])) {
        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO vendors (name, contact, services, payment_details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $contact, $services, $payment_details);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>New vendor added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    }
}
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="mb-3">
        <label class="form-label">Vendor Name:</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Contact Info:</label>
        <input type="text" name="contact" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Services Provided:</label>
        <input type="text" name="services" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Payment Details:</label>
        <input type="text" name="payment_details" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Add Vendor</button>
</form>

<?php include 'footer.php'; ?>
