<?php
session_start();
include 'db.php';
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
    $role = 'Customer'; // Default role for new users

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if (mysqli_query($conn, $query)) {
        echo "<p class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></p>";
    } else {
        echo "<p class='alert alert-danger'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Register</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<?php include 'footer.php'; ?>
