<?php
$connect=mysqli_connect("localhost","root","","procurement_database");
session_start();
require 'db_connections.php';

#if ($_SESSION['role']== '') {
 #   die($_SESSION['role']);
#}
if (isset($_POST["insert_button"])) {
    if ($_POST["insert"] == "yes") {
        $order_name = filter_input(INPUT_POST, "order_name", FILTER_SANITIZE_STRING);
        $item_name = filter_input(INPUT_POST, "item_name", FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, "status", FILTER_SANITIZE_STRING);
        $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);
        $vendor_name = filter_input(INPUT_POST, "vendor_name", FILTER_SANITIZE_STRING);
        $department = filter_input(INPUT_POST, "department", FILTER_SANITIZE_STRING);
        $priority_level = filter_input(INPUT_POST, "priority_level", FILTER_SANITIZE_STRING);
        $current_date = date('Y-m-d H:i:s');
        $mustContainLetter = "/[a-zA-Z]/";
        $blacklist = "/(\b(select|union|insert|update|delete|drop|alter|create|truncate|rename|load_file|outfile)\b|--|#|'|\")/i";
        if ($order_name == '' || $item_name=='') {
            echo "<center>Order or item name cannot be empty!</center><br>";
        }
        elseif ($quantity === false || $quantity < 0) {
            echo("<center>Error: Quantity must be a valid non-negative number!</center><br>");
        }
        elseif (
            preg_match($blacklist, $order_name) || 
            preg_match($blacklist, $item_name) || 
            preg_match($blacklist, $status) || 
            preg_match($blacklist, $vendor_name) || 
            preg_match($blacklist, $department) || 
            preg_match($blacklist, $priority_level)
        ) {
            echo("<center>Error: Invalid input detected!</center><br>");
        }elseif (
            !preg_match($mustContainLetter, $order_name) || 
            !preg_match($mustContainLetter, $item_name) 
        ) {
            echo("<center>Error: Order and Item must contain at least one alphabet letter!</center><br>");
        }
        else{
 // Correct the SQL statement with backticks and prepared statement usage
        $query = $connect->prepare("INSERT INTO `orders` (order_name, item_name, status, quantity, updated_date, vendor_name, department, priority_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters with appropriate types
        $query->bind_param("sssissss", $order_name, $item_name, $status, $quantity, $current_date, $vendor_name, $department, $priority_level);

        // Execute the query and check the result
        if ($query->execute()) {
            echo "<center>Record Inserted!</center><br>";
        } else {
            echo "<center>Error: " . $query->error . "</center><br>";
        }
    }
        }
       
}


if (isset($_POST["update_button"])) {
    if ($_POST["update"] == "yes") {

        // Sanitize & validate input
        $id = isset($_POST["order_id"]) ? filter_var($_POST["order_id"], FILTER_VALIDATE_INT) : null;
        $order_name = isset($_POST["order_name"]) ? trim(filter_var($_POST["order_name"], FILTER_SANITIZE_STRING)) : "";
        $item_name = isset($_POST["item_name"]) ? trim(filter_var($_POST["item_name"], FILTER_SANITIZE_STRING)) : "";
        $status = isset($_POST["status"]) ? trim(filter_var($_POST["status"], FILTER_SANITIZE_STRING)) : "";
        $quantity = isset($_POST["quantity"]) ? filter_var($_POST["quantity"], FILTER_VALIDATE_INT) : null;
        $vendor_name = isset($_POST["vendor_name"]) ? trim(filter_var($_POST["vendor_name"], FILTER_SANITIZE_STRING)) : "";
        $department = isset($_POST["department"]) ? trim(filter_var($_POST["department"], FILTER_SANITIZE_STRING)) : "";
        $priority_level = isset($_POST["priority_level"]) ? trim(filter_var($_POST["priority_level"], FILTER_SANITIZE_STRING)) : "";
        $updated_date = date('Y-m-d H:i:s');
        $mustContainLetter = "/[a-zA-Z]/";
        $blacklist = "/(\b(select|union|insert|update|delete|drop|alter|create|truncate|rename|load_file|outfile)\b|--|#|'|\")/i";
        // Ensure required fields are not empty
        if (empty($order_name) || empty($item_name) ) {
            echo "<center>Order or item name cannot be empty!</center><br>";
        }elseif ($quantity === false || $quantity < 0) {
            echo("<center>Error: Quantity must be a valid non-negative number!</center><br>");
        }elseif ($id === null || $quantity === null) {
            echo "<center>Invalid input data!</center><br>";
        }elseif (
            preg_match($blacklist, $order_name) || 
            preg_match($blacklist, $item_name) || 
            preg_match($blacklist, $status) || 
            preg_match($blacklist, $vendor_name) || 
            preg_match($blacklist, $department) || 
            preg_match($blacklist, $priority_level)
        ) {
            echo ("<center>Error: Invalid input detected!</center><br>");
        }elseif (
            !preg_match($mustContainLetter, $order_name) || 
            !preg_match($mustContainLetter, $item_name) 
        ) {
            echo("<center>Error: Order and Item must contain at least one alphabet letter!</center><br>");
        } else {
            // Prepare the query
            $query = $connect->prepare("
                UPDATE orders 
                SET order_NAME=?, item_name=?, status=?, quantity=?, updated_date=?, vendor_name=?, department=?, priority_level=?  
                WHERE order_ID=?
            ");

            if ($query) {
                // Bind the parameters
                $query->bind_param("sssissssi", $order_name, $item_name, $status, $quantity, $updated_date, $vendor_name, $department, $priority_level, $id);

                // Execute and check for errors
                if ($query->execute()) {
                    echo "<center>Record Updated Successfully!</center><br>";
                } else {
                    echo "<center>Error updating record: " . htmlspecialchars($query->error, ENT_QUOTES, 'UTF-8') . "</center><br>";
                }

                $query->close();
            } else {
                echo "<center>Error preparing statement: " . htmlspecialchars($connect->error, ENT_QUOTES, 'UTF-8') . "</center><br>";
            }
        }
    }
}



if(isset($_POST["delete_button"])){
    $id = isset($_POST["order_id"]) ? filter_var($_POST["order_id"], FILTER_VALIDATE_INT) : null;

    if ($id === null) {
        echo "<center>Error: Invalid Order ID!</center><br>";
    } else{
        $query=$connect->prepare("delete from orders where order_ID=?");
        $query->bind_param('i', $id);
        if($query->execute())
        {
            echo "<center>Record Deleted!</center><br>";
        }
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Record</title>
    <link rel="stylesheet" href="styles.css?v=1.1">

</head>
<body>
    <div class='topbox'>
        <h1>Order management</h1>
        <a class='logout' href='http://localhost/swap_project/admin.php'>Back</a>
        <a class='logout' href='http://localhost/swap_project/login.php'>Log out</a>
    </div>
    <form method="post" action="insertupdatedelete.php" >
        <table align="center" border="0" class='formbox' >
            <tr>
                <td>Order Name:</td>
                <td><input type="text" name="order_name" value="<?php echo isset($_GET['order_name']) ? htmlspecialchars($_GET['order_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Item name:</td>
                <td><input type="text" name="item_name" value="<?php echo isset($_GET['item_name']) ? htmlspecialchars($_GET['item_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                <select name='status' class='select' id="">
					<option value="Pending">Pending</option>
					<option value="Approved">Approved</option>
					<option value="Completed">Completed</option>
				</select>
                </td>
            </tr>
            <tr>
                <td>quantity:</td>
                <td><input type="number" name="quantity" value="<?php echo isset($_GET['quantity']) ? htmlspecialchars($_GET['quantity'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>vendor name:</td>
                <td><input type="text" name="vendor_name" value="<?php echo isset($_GET['vendor_name']) ? htmlspecialchars($_GET['vendor_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>department:</td>
                <td><input type="text" name="department" value="<?php echo isset($_GET['department']) ? htmlspecialchars($_GET['department'], ENT_QUOTES, 'UTF-8') : ''; ?>" /></td>
            </tr>
            <tr>
                <td>priority_level:</td>
                <td>
				<select name='priority_level' class='select' id="">
					<option value="low">low</option>
					<option value="medium">medium</option>
					<option value="high">high</option>
				</select>
			</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right">
                    <input type="hidden" name="insert" value="yes" />
                    <input type="submit" class='submitbutton' name="insert_button" value="Insert Record" />
                </td>
            </tr>
        </table>
        <form method="post" action="" >
        <div>
        <div id='sortbox'>
            <label for="sort_column" style="margin-left: 0.5em;">Sort By:</label>
            <select name="sort_column" class='sortinput'>
                <option value="order_id">Order ID</option>
                <option value="order_name">Order Name</option>
                <option value="creator_id">Creator ID</option>
                <option value="item_name">Item Name</option>
                <option value="status">Status</option>
                <option value="quantity">Quantity</option>
                <option value="updated_date">Updated Date</option>
                <option value="vendor_name">Vendor Name</option>
                <option value="department">Department</option>
                <option value="priority_level">Priority Level</option>
        </select>
        <select name="sort_order" class='sortinput'>
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
        </select>
        <input  type="submit" class='submitbutton' value="Sort" id='sortbut'>
        <div>
        </div>
    </form>
        <?php
$allowed_columns = ['order_id', 'order_name', 'creator_id', 'item_name', 'status', 'quantity', 'updated_date', 'vendor_name', 'department', 'priority_level'];

// Get sorting parameters from POST request
$sort_column = isset($_POST['sort_column']) && in_array($_POST['sort_column'], $allowed_columns) ? $_POST['sort_column'] : 'order_id';
$sort_order = isset($_POST['sort_order']) && $_POST['sort_order'] == 'desc' ? 'desc' : 'asc';        
$query=$connect->prepare("SELECT * FROM orders ORDER BY $sort_column $sort_order");
if ($sort_column == 'priority_level') {
    $query = $connect->prepare("SELECT * FROM orders ORDER BY FIELD(priority_level, 'Low', 'Medium', 'High') $sort_order");
} else {
    $query = $connect->prepare("SELECT * FROM orders ORDER BY $sort_column $sort_order");
}
$query->execute();
$query->bind_result($order_id, $order_name, $creator_id, $item_name, $status, $quantity, $updated_date, $vendor_name, $department, $priority_level);

echo "<table align='center' border='1'>";

echo "<tr>";
echo "<th>Id</th>";
echo "<th>Order name</th>";
echo "<th>Creator Id</th>";
echo "<th>Item name</th>";
echo "<th>Status</th>";
echo "<th>Quantity</th>";
echo "<th>Updated date</th>";
echo "<th>Vendor Name</th>";
echo "<th>Department</th>";
echo "<th>Priority level</th>";
echo "<th>EDIT</th>";
echo "<th>DELETE</th>";
echo "</tr>";

while ($query->fetch()) {
    echo "<tr>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($order_id) ? $order_id : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($order_name) ? $order_name : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($creator_id) ? $creator_id : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($item_name) ? $item_name : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($status) ? $status : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($quantity) ? $quantity : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($updated_date) ? $updated_date : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($vendor_name) ? $vendor_name : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($department) ? $department : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class='tableinfo'>" . htmlspecialchars(!empty($priority_level) ? $priority_level : 'empty', ENT_QUOTES, 'UTF-8') . "</td>";

    // Safely encode URL parameters
    $edit_url = "edit.php?operation=edit" .
        "&order_id=" . urlencode($order_id) .
        "&order_name=" . urlencode($order_name) .
        "&creator=" . urlencode($creator_id) .
        "&item_name=" . urlencode($item_name) .
        "&status=" . urlencode($status) .
        "&quantity=" . urlencode($quantity) .
        "&updated_date=" . urlencode($updated_date) .
        "&vendor_name=" . urlencode($vendor_name) .
        "&department=" . urlencode($department) .
        "&priority_level=" . urlencode($priority_level);

    echo "<td class='tableinfo'><a href='$edit_url'>edit</a></td>";

    echo "<td align='center' class='deletebox'>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') . "' />";
    echo "<input type='submit' class='submitbutton' name='delete_button' value='delete' onclick='return confirm(\"Are you sure you want to delete this order?\");' />";
    echo "</form>";
    echo "</td>";    
    echo "</tr>";    
}
echo "</table>";
?>
    </form>
</body>
</html>
