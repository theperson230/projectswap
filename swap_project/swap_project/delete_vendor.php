<?php
require 'db_connections.php';

// Must be admin
if (!canDeleteVendor()) {
    die("Unauthorized access. Admin only.");
}

if (!isset($_GET['vendor_id'])) {
    die("No vendor ID specified.");
}

$vendor_id = $_GET['vendor_id'];

// Use prepared statement for safety
$stmt = $conn->prepare("DELETE FROM vendor WHERE vendor_id=?");
$stmt->bind_param("i", $vendor_id);

$success = false;

if ($stmt->execute()) {
    $message = "<div class='success-msg'>Vendor deleted successfully.</div>";
    $success = true;
} else {
    $message = "<div class='error-msg'>Error: " . htmlspecialchars($stmt->error) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Vendor</title>
    <link rel="stylesheet" type="text/css" href="delete.css">
</head>
<body>

<div class="header">
    <h1>Delete Vendor</h1>
</div>

<div class="container">
    <?php echo $message; ?>
    <a href="read_records.php" class="btn-back">Back to Vendor List</a>
</div>

</body>
</html>
