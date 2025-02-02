<?php
require 'db_connections.php';

// Check read permission
if (!canReadVendor()) {
    die("Unauthorized access. Admin, Officer, or Dept Head only.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Vendors</title>
    <link rel="stylesheet" type="text/css" href="readRecord.css">
</head>
<body>

<div class="header">
    <h1>View Vendor Records</h1>
</div>

<div class="records-container">
    <?php
    // Query the vendor table (prepared statement not strictly needed if no user input, 
    // but let's keep consistent)
    $stmt = $conn->prepare("SELECT vendor_id, vendor_name, phone, service, terms FROM vendor");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Vendor ID</th>
                <th>Vendor Name</th>
                <th>Phone</th>
                <th>Service</th>
                <th>Payment Terms</th>
                <th>Actions</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            // Escape output to avoid XSS
            $vid     = htmlspecialchars($row['vendor_id']);
            $vname   = htmlspecialchars($row['vendor_name']);
            $vphone  = htmlspecialchars($row['phone']);
            $vserv   = htmlspecialchars($row['service']);
            $vterms  = htmlspecialchars($row['terms']);

            echo "<tr>";
            echo "<td>$vid</td>";
            echo "<td>$vname</td>";
            echo "<td>$vphone</td>";
            echo "<td>$vserv</td>";
            echo "<td>$vterms</td>";

            echo "<td class='actions-cell'>";
            // Edit if user can update
            if (canUpdateVendor()) {
                echo "<a class='btn-edit' href='update_vendor.php?vendor_id=$vid'>Update</a> ";
            }
            // Delete if user can delete
            if (canDeleteVendor()) {
                echo "<a class='btn-delete' href='delete_vendor.php?vendor_id=$vid'>Delete</a>";
            }
            echo "</td>";

            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-records'>No vendors found.</p>";
    }
    ?>

    <div class="back-button">
        <a href="admin.php" class="btn-back">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
