<?php
include 'header.php';  
include 'db_connect.php';

// Check if the vendors table exists
$check_table = $conn->query("SHOW TABLES LIKE 'vendors'");
if ($check_table->num_rows == 0) {
    echo "<div class='alert alert-danger'>Error: The 'vendors' table does not exist. Please create the table in phpMyAdmin.</div>";
    include 'footer.php';
    exit();
}

$sql = "SELECT * FROM vendors";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='table-dark'><tr><th>ID</th><th>Name</th><th>Contact</th><th>Services</th><th>Payment Details</th></tr></thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        $decrypted_payment = openssl_decrypt($row["payment_details"], "AES-128-CTR", "your_secret_key", 0, "1234567891011121");
        
        echo "<tr>
                <td>".htmlspecialchars($row["id"])."</td>
                <td>".htmlspecialchars($row["name"])."</td>
                <td>".htmlspecialchars($row["contact"])."</td>
                <td>".htmlspecialchars($row["services"])."</td>
                <td>".htmlspecialchars($decrypted_payment)."</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-warning'>No vendors found.</div>";
}

$conn->close();
include 'footer.php'; 
?>
