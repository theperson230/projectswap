<?php  
include 'header.php';  
include 'db_connect.php'; 

// Ensure session is only started if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure only Department Heads can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Department Head') {
    echo "<div class='alert alert-danger'>Access Denied! Only Department Heads can view procurement requests.</div>";
    include 'footer.php';
    exit();
}

// Check if the `procurement_requests` table exists
$check_table = $conn->query("SHOW TABLES LIKE 'procurement_requests'");
if ($check_table->num_rows == 0) {
    echo "<div class='alert alert-danger'>Error: The 'procurement_requests' table does not exist. Please create the table in phpMyAdmin.</div>";
    include 'footer.php';
    exit();
}

// Fetch procurement requests
$sql = "SELECT id, request_details, status FROM procurement_requests";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>Procurement Requests</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Request Details</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["id"]); ?></td>
                        <td><?php echo htmlspecialchars($row["request_details"]); ?></td>
                        <td>
                            <span class="badge <?php echo ($row["status"] == 'Approved') ? 'bg-success' : (($row["status"] == 'Rejected') ? 'bg-danger' : 'bg-warning'); ?>">
                                <?php echo htmlspecialchars($row["status"]); ?>
                            </span>
                        </td>
                        <td>
                            <!-- Approve Request -->
                            <form method="POST" action="update_request_status.php" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row["id"]); ?>">
                                <input type="hidden" name="status" value="Approved">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>

                            <!-- Reject Request -->
                            <form method="POST" action="update_request_status.php" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row["id"]); ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No procurement requests found.</div>
    <?php endif; ?>
</div>

<?php 
$conn->close();
include 'footer.php'; 
?>
