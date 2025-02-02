<?php  
session_start();
include 'db.php'; // Ensure this file exists
include 'header.php'; // Include the header for navigation

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$query = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $role);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];

    $update_query = "UPDATE users SET username = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_username, $user_id);

    if ($update_stmt->execute()) {
        echo '<div class="alert alert-success">Profile updated successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error updating profile: ' . $update_stmt->error . '</div>';
    }

    $update_stmt->close();
}
?>

<div class="container">
    <div class="login-container">
        <h2 class="text-center">My Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label>Role:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($role); ?>" disabled>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
