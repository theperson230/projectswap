<?php
session_start();
include 'header.php'; 
include 'db_connect.php'; 

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Simulate sending a reset link (You can replace this with actual email sending)
        echo "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
    } else {
        echo "<div class='alert alert-danger'>Email not found in our records!</div>";
    }
    $stmt->close();
}
?>

<div class="container mt-4">
    <h2>Forgot Password</h2>
    <p>Enter your email to receive a password reset link.</p>
    <form method="POST">
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>

<?php include 'footer.php'; ?>
