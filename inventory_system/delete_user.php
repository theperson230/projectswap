<?php
include 'db_connect.php';
session_start();

// Ensure only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    die("<div class='alert alert-danger'>Access Denied! Only Admins can delete users.</div>");
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("<div class='alert alert-danger'>CSRF token validation failed!</div>");
}

// Validate User ID input
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if (!$id) {
    die("<div class='alert alert-danger'>Invalid user ID.</div>");
}

// Prevent Admin from deleting their own account
if ($_SESSION['username'] === getUsernameById($id, $conn)) {
    die("<div class='alert alert-warning'>You cannot delete your own account.</div>");
}

// Delete user from database
$sql = "DELETE FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>User deleted successfully! Redirecting...</div>";
    header("refresh:2;url=manage_users.php"); // Redirect after 2 seconds
} else {
    echo "<div class='alert alert-danger'>Error deleting user: " . $stmt->error . "</div>";
}
$stmt->close();
$conn->close();

// Function to get username by ID
function getUsernameById($id, $conn) {
    $query = "SELECT username FROM users WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
    return $username;
}
?>
