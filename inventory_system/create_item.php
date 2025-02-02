<?php 
include 'header.php';  
include 'db_connect.php'; // No need to start session again

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Sanitize input to prevent XSS and SQL Injection
    $product_name = htmlspecialchars(strip_tags($_POST['product_name']));
    $sku = htmlspecialchars(strip_tags($_POST['sku']));
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $restock_level = filter_var($_POST['restock_level'], FILTER_VALIDATE_INT);

    if ($quantity === false || $restock_level === false) {
        echo "<div class='alert alert-danger'>Invalid input! Quantity and Restock Level must be numbers.</div>";
    } elseif (!empty($product_name) && !empty($sku)) {
        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO inventory (product_name, sku, quantity, restock_level) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $product_name, $sku, $quantity, $restock_level);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>New inventory item added successfully!</div>";
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
        <label class="form-label">Product Name:</label>
        <input type="text" name="product_name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">SKU:</label>
        <input type="text" name="sku" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Quantity:</label>
        <input type="number" name="quantity" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Restock Level:</label>
        <input type="number" name="restock_level" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Add Item</button>
</form>

<?php include 'footer.php'; ?>
