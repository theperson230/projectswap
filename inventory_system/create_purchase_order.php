<?php
session_start();
include 'db.php';
include 'header.php';

// Restrict access - Only Procurement Officers can create orders
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Procurement Officer") {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = $_POST['vendor_id'];
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];
    $csrf_token = $_POST['csrf_token'];

    // CSRF Token Validation
    if ($csrf_token !== $_SESSION['csrf_token']) {
        $error_message = "Invalid request!";
    } else {
        // Check if vendor exists
        $vendor_check = mysqli_query($conn, "SELECT id FROM vendors WHERE id = '$vendor_id'");
        if (mysqli_num_rows($vendor_check) == 0) {
            $error_message = "Error: Vendor ID does not exist.";
        }

        // Check if item exists
        $item_check = mysqli_query($conn, "SELECT id FROM inventory WHERE id = '$item_id'");
        if (mysqli_num_rows($item_check) == 0) {
            $error_message = "Error: Item ID does not exist.";
        }

        // If no errors, insert purchase order
        if ($error_message == "") {
            $query = "INSERT INTO purchase_orders (vendor_id, item_id, quantity, status) VALUES ('$vendor_id', '$item_id', '$quantity', 'Pending')";
            if (mysqli_query($conn, $query)) {
                $success_message = "Purchase order created successfully!";
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Create Purchase Order</h2>
    
    <?php if ($error_message) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <?php if ($success_message) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <label>Vendor ID:</label>
            <input type="text" name="vendor_id" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Item ID:</label>
            <input type="text" name="item_id" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Quantity:</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Create Purchase Order</button>
    </form>
</div>

<?php include 'footer.php'; ?>
