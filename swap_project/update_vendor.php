<?php
require 'db_connections.php';

// Check permission
if (!canUpdateVendor()) {
    die("Unauthorized access. Only Admin/Officer can update.");
}

// Determine vendor_id
if (isset($_GET['vendor_id'])) {
    $vendor_id = $_GET['vendor_id'];
} elseif (isset($_POST['vendor_id'])) {
    $vendor_id = $_POST['vendor_id'];
} else {
    die("No vendor ID specified.");
}

$message = "";

// If form posted, handle update
if (isset($_POST['update_vendor'])) {
    $vendor_name = trim($_POST['vendor_name'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $service     = trim($_POST['service'] ?? '');
    $terms       = trim($_POST['terms'] ?? '');

    // Length checks (same logic as create)
    if (strlen($vendor_name) > 45) {
        $message = "<div class='error-msg'>Vendor Name exceeds 45 characters.</div>";
    } elseif (strlen($phone) > 20) {
        $message = "<div class='error-msg'>Phone exceeds 20 characters.</div>";
    } elseif (strlen($service) > 100) {
        $message = "<div class='error-msg'>Service description exceeds 100 characters.</div>";
    } elseif (strlen($terms) > 50) {
        $message = "<div class='error-msg'>Payment Terms exceed 50 characters.</div>";
    } else {
        // Update with prepared statement
        $stmt = $conn->prepare("
            UPDATE vendor 
            SET vendor_name=?, phone=?, service=?, terms=? 
            WHERE vendor_id=?
        ");
        $stmt->bind_param("ssssi", $vendor_name, $phone, $service, $terms, $vendor_id);

        if ($stmt->execute()) {
            $message = "<div class='success-msg'>Vendor updated successfully.</div>";
        } else {
            $message = "<div class='error-msg'>Error updating vendor: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
}

// Fetch the vendor to populate the form
$stmt = $conn->prepare("SELECT * FROM vendor WHERE vendor_id=?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();
if (!$vendor) {
    die("<div class='error-msg'>Vendor not found.</div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Vendor</title>
    <link rel="stylesheet" type="text/css" href="update.css">
</head>
<body>

<div class="header">
    <h1>Update Vendor</h1>
</div>

<div class="form-container">
    <?php echo $message; ?>

    <form method="POST" action="update_vendor.php?vendor_id=<?php echo htmlspecialchars($vendor_id); ?>">
        <input type="hidden" name="vendor_id" value="<?php echo htmlspecialchars($vendor_id); ?>">

        <label for="vendor_name">Vendor Name:</label>
        <input type="text" name="vendor_name" value="<?php echo htmlspecialchars($vendor['vendor_name']); ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($vendor['phone']); ?>" required>

        <label for="service">Services Provided:</label>
        <input type="text" name="service" value="<?php echo htmlspecialchars($vendor['service']); ?>" required>

        <label for="terms">Payment Terms:</label>
        <input type="text" name="terms" value="<?php echo htmlspecialchars($vendor['terms']); ?>" required>

        <button type="submit" name="update_vendor" class="btn-update">Update Vendor</button>
        <a href="read_records.php" class="btn-cancel">Back</a>
    </form>
</div>

</body>
</html>
