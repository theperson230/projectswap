<?php
$connect=mysqli_connect("localhost","root","","procurement_database");

if (isset($_POST["insert_button"])) {
    if ($_POST["insert"] == "yes") {
        $order_name = $_POST["order_name"];
        $item_name = $_POST["item_name"];
        $status = $_POST["status"];
        $quantity = $_POST["quantity"];
        $vendor_name = $_POST["vendor_name"];
        $department = $_POST["department"];
        $priority_level = $_POST["priority_level"];
        $current_date = date('Y-m-d H:i:s');
        if ($order_name == '' || $item_name=='') {
            echo "<center>Order or item name cannot be empty!</center><br>";
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


if(isset($_POST["update_button"])){
	if($_POST["update"]=="yes")
	{
		$id=$_POST["order_id"];
		$order_name=$_POST["order_name"];
        $item_name=$_POST['item_name'];
		$status=$_POST["status"];
        $quantity=$_POST["quantity"];
        $vendor_name=$_POST['vendor_name'];
		$department=$_POST["department"];
		$priority_level=$_POST["priority_level"];
        $updated_date = date('Y-m-d H:i:s');
	

		$query=$connect->prepare("update orders set order_NAME=?, item_name=?, status=?, quantity=?, updated_date=?, vendor_name=?, department=?, priority_level=?  where order_ID=?");
		$query->bind_param("sssissssi", $order_name, $item_name, $status, $quantity, $updated_date, $vendor_name, $department, $priority_level, $id);//bind the parameters
		if($query->execute())
		{
			echo "<center>Record Updated!</center><br>";
            echo '<pre>'; print_r($_POST); echo '</pre>';
		}
	}
}


if(isset($_POST["delete_button"])){
	$id=$_POST["order_id"];
	$query=$connect->prepare("delete from orders where order_ID=?");
	$query->bind_param('i', $id);
	if($query->execute())
	{
		echo "<center>Record Deleted!</center><br>";
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
        <button class='logout'>Log out</button>
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
            <label for="sort_column" style="margin-left: 13em;">Sort By:</label>
            <select name="sort_column">
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
        <select name="sort_order">
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
        </select>
        <input  type="submit" value="Sort">
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
    echo "<td class='tableinfo'>" . (!empty($order_id) ? $order_id : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($order_name) ? $order_name : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($creator_id) ? $creator_id : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($item_name) ? $item_name : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($status) ? $status : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($quantity) ? $quantity : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($updated_date) ? $updated_date : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($vendor_name) ? $vendor_name : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($department) ? $department : 'empty') . "</td>";
    echo "<td class='tableinfo'>" . (!empty($priority_level) ? $priority_level : 'empty') . "</td>";
    
    echo "<td class='tableinfo'><a href='edit.php?operation=edit&order_id=" . 
        (!empty($order_id) ? $order_id : 'empty') . "&order_name=" . 
        (!empty($order_name) ? $order_name : 'empty') . "&creator=" . 
        (!empty($creator_id) ? $creator_id : 'empty') . "&item_name=" . 
        (!empty($item_name) ? $item_name : 'empty') . "&status=" . 
        (!empty($status) ? $status : 'empty') . "&quantity=" . 
        (!empty($quantity) ? $quantity : 'empty') . "&updated_date=" . 
        (!empty($updated_date) ? $updated_date : 'empty') . "&vendor_name=" . 
        (!empty($vendor_name) ? $vendor_name : 'empty') . "&department=" . 
        (!empty($department) ? $department : 'empty') . "&priority_level=" . 
        (!empty($priority_level) ? $priority_level : 'empty') . "'>edit</a></td>";

    echo "<td align='center' class='deletebox'>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='order_id' value='" . (!empty($order_id) ? $order_id : 'empty') . "' />";
    echo "<input type='submit' name='delete_button' value='delete' />";
    echo "</form>";
    echo "</td>";    
    echo "</tr>";    
}
echo "</table>";
?>
    </form>
</body>
</html>
