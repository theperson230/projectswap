<?php
session_start();
include 'db.php';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Prevent brute force attacks - Track login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['login_attempts'] >= 5) {
        $error = "Too many failed login attempts. Please try again later.";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Validate input
        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            // Prevent SQL Injection
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true); // Prevent session fixation attacks

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = htmlspecialchars($user['username']);
                $_SESSION['role'] = $user['role'];

                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;

                // Redirect users based on role
                switch ($user['role']) {
                    case 'Admin':
                        header("Location: dashboard.php");
                        exit();
                    case 'Procurement Officer':
                        header("Location: manage_inventory.php");
                        exit();
                    case 'Department Head':
                        header("Location: view_procurement_requests.php");
                        exit();
                    case 'Customer':
                    case 'User': // Redirects both "Customer" and "User" to user dashboard
                        header("Location: user_dashboard.php");
                        exit();
                    default:
                        session_destroy();
                        $error = "Invalid role detected.";
                }
            } else {
                $_SESSION['login_attempts']++; // Increment failed attempts
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    }
}
?>

<?php include 'header.php'; ?>
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="col-md-4">
        <div class="card shadow p-4">
            <h2 class="text-center">Login</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
