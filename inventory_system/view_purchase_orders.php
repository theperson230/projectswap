<?php
session_start();
include 'db.php';

$query = "SELECT po.id, v.vendor_name, i.product_name, po.quantity, po.status 
          FROM purchase_orders po
          JOIN vendors v ON po.vendor_id = v.id
          JOIN inventory i ON po.item_id = i.id";
$result = mysqli_query($conn, $query);
?>

<table border="1">
    <tr>
        <th>Order ID</th>
        <th>Vendor</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['vendor_name'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                    <select name="status">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
