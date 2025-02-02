<?php 
session_start();
include 'header.php';  
include 'db_connect.php'; 

// Ensure only "User" and "Customer" roles can access this page
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'User' && $_SESSION['role'] !== 'Customer')) {
    header("Location: login.php?error=unauthorized_access");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // Sanitize and validate input
    $item_id = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if ($quantity === false || $quantity <= 0) {
        echo "<div class='alert alert-danger'>Invalid input! Quantity must be a positive number.</div>";
    } elseif (!empty($item_id)) {
        // Check if the requested quantity is available
        $stmt = $conn->prepare("SELECT product_name, quantity FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        if (!$item || $item['quantity'] < $quantity) {
            echo "<div class='alert alert-danger'>Error: Not enough stock available.</div>";
        } else {
            // Deduct stock and insert order
            $stmt = $conn->prepare("INSERT INTO purchase_orders (user_id, item_id, quantity, status) VALUES (?, ?, ?, 'Pending')");
            $stmt->bind_param("iii", $user_id, $item_id, $quantity);

            if ($stmt->execute()) {
                // Update inventory stock
                $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                $stmt->bind_param("ii", $quantity, $item_id);
                $stmt->execute();

                echo "<div class='alert alert-success'>Order placed successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    } else {
        echo "<div class='alert alert-danger'>Please select an item.</div>";
    }
}

// Fetch available inventory items
$result = $conn->query("SELECT id, product_name, quantity FROM inventory WHERE quantity > 0");
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <div class="mb-3">
        <label class="form-label">Select Item:</label>
        <select name="item_id" class="form-control" required>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['product_name']) . " (Stock: " . $row['quantity'] . ")"; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Quantity:</label>
        <input type="number" name="quantity" class="form-control" min="1" required>
    </div>

    <button type="submit" class="btn btn-primary">Place Order</button>
</form>

<?php include 'footer.php'; ?>
