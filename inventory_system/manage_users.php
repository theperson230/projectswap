<?php
include 'header.php';
include 'db_connect.php';

// Ensure only Admin can manage users
if ($_SESSION['role'] !== 'Admin') {
    echo "<div class='alert alert-danger'>Access Denied!</div>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }

    // User creation
    if (isset($_POST['create_user'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = htmlspecialchars($_POST['role']);

        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            logAction($_SESSION['username'], "Created new user: $username with role $role", $conn);
            echo "<div class='alert alert-success'>User created successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error creating user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    // User update
    if (isset($_POST['update_user'])) {
        $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
        $role = htmlspecialchars($_POST['role']);

        $sql = "UPDATE users SET role=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $role, $user_id);

        if ($stmt->execute()) {
            logAction($_SESSION['username'], "Updated user ID $user_id to role $role", $conn);
            echo "<div class='alert alert-success'>User updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }

    // User deletion
    if (isset($_POST['delete_user'])) {
        $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);

        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            logAction($_SESSION['username'], "Deleted user ID $user_id", $conn);
            echo "<div class='alert alert-success'>User deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting user: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!-- Form with CSRF Token -->
<form method="POST" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <!-- Create User -->
    <h3>Create User</h3>
    <input type="text" name="username" placeholder="Username" class="form-control mb-2" required>
    <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
    <select name="role" class="form-control mb-2">
        <option value="Admin">Admin</option>
        <option value="Procurement Officer">Procurement Officer</option>
        <option value="Department Head">Department Head</option>
    </select>
    <button type="submit" name="create_user" class="btn btn-success">Create User</button>

    <!-- Update User -->
    <
