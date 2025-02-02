<?php
$host = "localhost";
$user = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "inventory_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Logging function
function logAction($user, $action, $conn) {
    $sql = "INSERT INTO logs (user, action) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $action);
    $stmt->execute();
    $stmt->close();
}
?>
