<?php
include 'header.php';
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Ensure only Admin can view logs
if ($_SESSION['role'] !== 'Admin') {
    echo "<div class='alert alert-danger'>Access Denied! Only Admins can view logs.</div>";
    exit();
}

// Filtering logs
$filter_user = isset($_GET['user']) ? $_GET['user'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

$sql = "SELECT * FROM logs WHERE 1";
$params = [];
$types = "";

// Apply filters if selected
if (!empty($filter_user)) {
    $sql .= " AND user=?";
    $params[] = $filter_user;
    $types .= "s";
}
if (!empty($filter_date)) {
    $sql .= " AND DATE(timestamp) = ?";
    $params[] = $filter_date;
    $types .= "s";
}

$sql .= " ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);

// Bind parameters dynamically
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>System Logs</h2>

    <!-- Filter Form -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="user" class="form-control" placeholder="Filter by User" value="<?php echo htmlspecialchars($filter_user); ?>">
            </div>
            <div class="col-md-4">
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="view_logs.php" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo htmlspecialchars($row["user"]); ?></td>
                    <td><?php echo htmlspecialchars($row["action"]); ?></td>
                    <td><?php echo $row["timestamp"]; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
