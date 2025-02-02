<?php 
include 'header.php';  
include 'db_connect.php'; 

// Get search values from request
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Build SQL query dynamically
$sql = "SELECT * FROM vendors WHERE 1";

if (!empty($search)) {
    $sql .= " AND (vendor_name LIKE ? OR contact_info LIKE ?)";
}

$stmt = $conn->prepare($sql);

// Bind parameters based on inputs
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container mt-4">
    <h2>Manage Vendors</h2>

    <!-- Search Form -->
    <form method="GET" class="row mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by Vendor Name or Contact" value="<?php echo $search; ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="manage_vendors.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Vendor Name</th>
                    <th>Contact Info</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["id"]); ?></td>
                        <td><?php echo htmlspecialchars($row["vendor_name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["contact_info"]); ?></td>
                        <td>
                            <a href="update_vendor.php?id=<?php echo htmlspecialchars($row["id"]); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" action="delete_vendor.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this vendor?');">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row["id"]); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No vendors found.</div>
    <?php endif; ?>
</div>

<?php 
$stmt->close();
$conn->close();
include 'footer.php'; 
?>
