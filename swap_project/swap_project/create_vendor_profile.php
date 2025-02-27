<?php
require 'db_connections.php';

// ✅ Check if user has permission to create vendors (Only Admin & Procurement Officer)
if (!canCreateVendor()) {
    die("Unauthorized access. Only Admin/Procurement Officer can create vendors.");
}

// ✅ Initialize message variable for feedback
$message = "";

// ✅ Function to sanitize user input (Prevents XSS and trims spaces)
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// ✅ Handle form submission when user submits the "Create Vendor" form
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_vendor'])) {
    // 🟢 Sanitize user input before processing
    $vendor_name = sanitizeInput($_POST['vendor_name'] ?? '');
    $phone       = sanitizeInput($_POST['phone'] ?? '');
    $service     = sanitizeInput($_POST['service'] ?? '');
    $terms       = sanitizeInput($_POST['terms'] ?? '');

    // ✅ SERVER-SIDE VALIDATION (Prevents Invalid Data)
    if (empty($vendor_name) || empty($phone) || empty($service) || empty($terms)) {
        $message = "⚠️ All fields are required.";
    } elseif (!preg_match('/^[a-zA-Z0-9\s]{3,45}$/', $vendor_name)) {
        $message = "⚠️ Invalid Vendor Name! Must be 3-45 alphanumeric characters.";
    } elseif (!preg_match('/^\d{8,20}$/', $phone)) {
        $message = "⚠️ Invalid Phone Number! Must be 8-20 digits.";
    } elseif (strlen($service) > 100) {
        $message = "⚠️ Service cannot exceed 100 characters.";
    } elseif (strlen($terms) > 50) {
        $message = "⚠️ Payment Terms cannot exceed 50 characters.";
    } else {
        // ✅ INSERT INTO DATABASE using a SQL Prepared Statement (Prevents SQL Injection)
        $stmt = $conn->prepare("
            INSERT INTO vendor (vendor_name, phone, service, terms)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $vendor_name, $phone, $service, $terms);

        // ✅ Execute query and provide feedback
        if ($stmt->execute()) {
            $message = "✅ New vendor created successfully.";
        } else {
            $message = "❌ Error: " . htmlspecialchars($stmt->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Vendor</title>
    <link rel="stylesheet" type="text/css" href="createVendor.css">

    <!-- ✅ JavaScript Client-Side Validation to Prevent Invalid Data Submission -->
    <script>
        function validateForm() {
            let vendorName = document.getElementById("vendor_name").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let service = document.getElementById("service").value.trim();
            let terms = document.getElementById("terms").value.trim();

            let namePattern = /^[a-zA-Z0-9\s]{3,45}$/; // Vendor Name must be alphanumeric (3-45 chars)
            let phonePattern = /^\d{8,20}$/; // Phone must be digits only (8-20 chars)

            // 🟠 Validate Vendor Name
            if (!namePattern.test(vendorName)) {
                alert("⚠️ Invalid input for Vendor Name! Use only letters, numbers, and spaces.");
                return false;
            }
            // 🟠 Validate Phone Number
            if (!phonePattern.test(phone)) {
                alert("⚠️ Invalid input for Phone! Must be 8-20 digits.");
                return false;
            }
            // 🟠 Validate Service Length
            if (service.length > 100) {
                alert("⚠️ Service cannot exceed 100 characters!");
                return false;
            }
            // 🟠 Validate Payment Terms Length
            if (terms.length > 50) {
                alert("⚠️ Payment Terms cannot exceed 50 characters!");
                return false;
            }

            return true; // ✅ Form is valid, allow submission
        }
    </script>
</head>
<body>

<!-- ✅ Page Header -->
<div class="header">
    <h1>Create Vendor</h1>
</div>

<!-- ✅ Form Container -->
<div class="form-container">
    <!-- ✅ Display any messages from the backend -->
    <?php if (!empty($message)): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- ✅ FORM: Submits to the same page with validation before submission -->
    <form action="" method="POST" onsubmit="return validateForm();">
        <label>Vendor Name:</label>
        <input type="text" id="vendor_name" name="vendor_name" required>

        <label>Phone:</label>
        <input type="text" id="phone" name="phone" required>

        <label>Services Provided:</label>
        <input type="text" id="service" name="service" required>

        <label>Payment Terms:</label>
        <input type="text" id="terms" name="terms" required>

        <!-- ✅ Submit and Back Buttons -->
        <div class="button-row">
            <input type="submit" name="create_vendor" value="Create Vendor">
            <a href="admin.php" class="btn-back">Back</a>
        </div>
    </form>
</div>

</body>
</html>
