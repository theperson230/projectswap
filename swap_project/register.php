<?php
session_start();

// 1) Connect to DB
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "procurement_database"; // Adjust to your real DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// 2) If the user submitted the registration form:
if (isset($_POST['register'])) {
    // Gather inputs
    $role       = $_POST['role']       ?? '';
    $name       = $_POST['name']       ?? '';
    $email      = $_POST['email']      ?? '';
    $plainPass  = $_POST['password']   ?? '';

    // Trim them
    $role       = trim($role);
    $name       = trim($name);
    $email      = trim($email);
    $plainPass  = trim($plainPass);

    // Basic validation
    if ($role === "" || $name === "" || $email === "" || $plainPass === "") {
        $message = "All fields are required.";
    } else {
        // Hash the password
        $hashedPass = password_hash($plainPass, PASSWORD_DEFAULT);

        // Decide which table to insert into
        if ($role === 'admin') {
            // Insert into admin table
            $stmt = $conn->prepare("INSERT INTO admin (admin_name, email, password) VALUES (?, ?, ?)");
        } elseif ($role === 'officer') {
            // Insert into officer table
            $stmt = $conn->prepare("INSERT INTO officer (officer_name, email, password) VALUES (?, ?, ?)");
        } elseif ($role === 'head') {
            // Insert into department_head table
            $stmt = $conn->prepare("INSERT INTO department_head (head_name, email, password) VALUES (?, ?, ?)");
        } else {
            $message = "Invalid role selected.";
        }

        // If $stmt got created, execute it
        if (!empty($stmt)) {
            $stmt->bind_param("sss", $name, $email, $hashedPass);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                // e.g. if email is duplicate or table constraints
                $message = "Error: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="register.css">
</head>
<body>

<div class="register-container">
    <h1>Register</h1>
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="input-group">
            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="officer">Procurement Officer</option>
                <option value="head">Department Head</option>
            </select>
        </div>

        <div class="input-group">
            <label for="name">Full Name:</label>
            <input type="text" name="name" required>
        </div>

        <div class="input-group">
            <label for="email">Email Address:</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </div>

        <button class="register-btn" type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
