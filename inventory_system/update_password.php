<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $query = "UPDATE users SET password='$new_password' WHERE id='$user_id'";
    if (mysqli_query($conn, $query)) {
        echo "Password updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
