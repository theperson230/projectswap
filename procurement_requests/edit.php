<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Record</title>
    <link rel="stylesheet" href="styles.css?v=1.1">
</head>
<body>
<h2 class='edittitle'>Update Order</h2>
<h2 class='edittitle'><?php echo isset($_GET['order_name']) ? htmlspecialchars($_GET['order_name'], ENT_QUOTES, 'UTF-8') : ''; ?></h2>
    <form method="post" action="insertupdatedelete.php" >
        <table align="center" border="0" class='formbox' id='editbox'>
        <tr >
                <td>Order Name:</td>
                <td><input type="text" name="order_name" value="<?php echo isset($_GET['order_name']) ? htmlspecialchars($_GET['order_name'], ENT_QUOTES, 'UTF-8') : ''; ?>"  require/></td>
            </tr>
            <tr>
                <td>Item name:</td>
                <td><input type="text" name="item_name" value="<?php echo isset($_GET['item_name']) ? htmlspecialchars($_GET['item_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" require/></td>
            </tr>
            <tr>
                <td>Status:</td>
                <td>
                <select name='status' class='select' id="" >
                    <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="Completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
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
				<select name='priority_level' class='select' id="" >
                    <option value="low" <?php echo (isset($_GET['priority_level']) && $_GET['priority_level'] == 'low') ? 'selected' : ''; ?>>low</option>
                    <option value="medium" <?php echo (isset($_GET['priority_level']) && $_GET['priority_level'] == 'medium') ? 'selected' : ''; ?>>medium</option>
                    <option value="high" <?php echo (isset($_GET['priority_level']) && $_GET['priority_level'] == 'high') ? 'selected' : ''; ?>>high</option>
				</select>
			</td>
            </tr>
            <tr>
            <tr>
                <td>&nbsp;</td>
                <td align="right">
                    <input type="hidden" name="order_id" value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id'], ENT_QUOTES, 'UTF-8') : ''; ?>" />
                    <input type="hidden" name="update" value="yes" />
                    <input type="submit" class='submitbutton' name="update_button" value="Update Record" />
                    <a href="insertupdatedelete.php">Go back</a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
