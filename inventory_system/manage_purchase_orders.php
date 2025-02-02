<?php
session_start();
include 'db.php';
include 'header.php';

// Restrict access - Only Admin and Procurement Officers
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != "Admin" && $_SESSION['role'] != "Procurement Officer")) {
    $_SESSION['error'] = "Access denied. You do not have permission to view this page.";
    header("Location: dashboard.php");
    exit();
}

// Messages for success or error handling
$message = "";
$message_type = "";

// Handle status update securely
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE purchase_orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        $message = "Order status updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating order status: " . $stmt->error;
        $message_type = "danger";
    }

    $stmt->close();
}

// Handle delete order securely
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    // Only allow deletion if order is still pending
    $stmt = $conn->prepare("SELECT status FROM purchase_orders WHERE id=?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['status'] == "Pending") {
        $stmt = $conn->prepare("DELETE FROM purchase_orders WHERE id=?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $message = "Order deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting order: " . $stmt->error;
            $message_type = "danger";
        }

        $stmt->close();
    } else {
        $message = "Only pending orders can be deleted!";
        $message_type = "warning";
    }
}

// Fetch all purchase orders securely
$query = "SELECT po.id, v.vendor_name, i.product_name, po.quantity, po.status 
          FROM purchase_orders po 
          JOIN vendors v ON po.vendor_id = v.id 
          JOIN inventory i ON po.item_id = i.id
          ORDER BY po.id DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <h2 class="text-center">Manage Purchase Orders</h2>

    <?php if ($message) { ?>
        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php } ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Vendor</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Update Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                            <select name="status" class="form-control">
                                <option value="Pending" <?php if ($row['status'] == "Pending") echo "selected"; ?>>Pending</option>
                                <option value="Approved" <?php if ($row['status'] == "Approved") echo "selected"; ?>>Approved</option>
                                <option value="Completed" <?php if ($row['status'] == "Completed") echo "selected"; ?>>Completed</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-success mt-2">Update</button>
                        </form>
                    </td>
                    <td>
                        <?php if ($row['status'] == "Pending") { ?>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_order" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
