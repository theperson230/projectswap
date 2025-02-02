<?php
session_start();
require 'db_connections.php';

$message = "";

// LOGIN LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $role     = $_POST['role'] ?? '';
    $email    = $_POST['email'] ?? '';
    $plainPass= $_POST['password'] ?? '';

    // Trim inputs
    $role     = trim($role);
    $email    = trim($email);
    $plainPass= trim($plainPass);

    // Input validation
    if (empty($role) || empty($email) || empty($plainPass)) {
        $message = "Please fill in all fields.";
    } else {
        // Determine the correct table based on role
        switch ($role) {
            case 'admin':
                $stmt = $conn->prepare("SELECT admin_id, admin_name, email, password FROM admin WHERE email=? LIMIT 1");
                break;
            case 'procurement_officer':
                $stmt = $conn->prepare("SELECT officer_id, officer_name, email, password FROM officer WHERE email=? LIMIT 1");
                break;
            case 'department_head':
                $stmt = $conn->prepare("SELECT head_id, head_name, email, password FROM department_head WHERE email=? LIMIT 1");
                break;
            default:
                $message = "Invalid role selected.";
                $stmt = null;
        }

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($plainPass, $row['password'])) {
                    // Store role & user info in session
                    $_SESSION['role'] = $role;

                    if ($role === 'admin') {
                        $_SESSION['user_id'] = $row['admin_id'];
                        $_SESSION['name']    = $row['admin_name'];
                        header("Location: admin.php"); // Redirect Admins
                    } elseif ($role === 'procurement_officer') {
                        $_SESSION['user_id'] = $row['officer_id'];
                        $_SESSION['name']    = $row['officer_name'];
                        header("Location: admin.php"); // Redirect Officers
                    } elseif ($role === 'department_head') {
                        $_SESSION['user_id'] = $row['head_id'];
                        $_SESSION['name']    = $row['head_name'];
                        header("Location: headDEP.php"); // Redirect Department Heads
                    }
                    exit();
                } else {
                    $message = "Invalid password.";
                }
            } else {
                $message = "Account not found for this role/email.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
<body>

<div class="login-container">
    <h1>Login</h1>
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="input-group">
            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="procurement_officer">Procurement Officer</option>
                <option value="department_head">Department Head</option>
            </select>
        </div>

        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </div>

        <button class="login-btn" type="submit" name="login">Login</button>
    </form>

    <p>Don’t have an account? <a href="register.php">Register here</a>.</p>
</div>

</body>
</html>
