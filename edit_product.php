<?php
	session_start();
	
	include('header.php');
	include('config.php');
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	if($_SESSION['level'] != 1)
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
		
	if(isset($_POST['submitButton']))
	{
		$product_id = mysql_real_escape_string($_POST['product_id']);
		$product_code = mysql_real_escape_string($_POST['product_code']);
		$product_type = mysql_real_escape_string($_POST['product_type']);
		$product_desc = mysql_real_escape_string($_POST['product_desc']);
		$timestamp = mysql_real_escape_string($_POST['timestamp']);
		$quantity = $_POST['quantity'];
		$unit_price = $_POST['unit_price'];
		$extended_price = $_POST['extended_price'];
		
		if(!empty($product_code) and !empty($product_type))
		{
			// Remove all prices associated with this product
			$q = "DELETE FROM prices WHERE product_id = $product_id";
			mysql_query($q, $conn);
			
			
			// Insert new prices associated with this product
			$q = "UPDATE products SET product_code='$product_code', product_type='$product_type', description='$product_desc', timestamp='$timestamp' WHERE product_id = $product_id";
			$r = mysql_query($q, $conn);
			
			for($i = 0; $i < count($quantity); $i++)
			{
				$num = mysql_real_escape_string($quantity[$i]);
				$price = mysql_real_escape_string($unit_price[$i]);
				$extended = mysql_real_escape_string($extended_price[$i]);
				
				$q = "INSERT INTO prices (product_id, quantity, unit_price, extended_price)
						VALUES('$product_id', '$num', '$price', '$extended')";
				mysql_query($q, $conn);
			}
		}
	}
	
	$q = "SELECT *, DATE_FORMAT(timestamp,'%Y-%m-%d') AS date FROM products WHERE product_id = " . mysql_real_escape_string($_GET['id']);
	$r = mysql_query($q, $conn);
	$product = mysql_fetch_assoc($r);
?>
		<div id="content" style="text-align:center;">
			<form name="newProduct" method="post" action="">
				<h2 style="text-align:center;">Edit Product</h2>
				<input type="hidden" name="product_id" value="<?php echo $_GET['id']; ?>" />
				<table id="new_product" cellpadding="5">
					<tr>
						<td colspan="3" style="text-align:center;"><b id="msg"></b></td>
					</tr>
					<tr>
						<td colspan="2">
							Product Code:&nbsp;&nbsp;
							<input type="text" name="product_code" id="product_code" size="10" value="<?php echo $product['product_code']; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;
							
							Product Type:&nbsp;&nbsp;
							<select name="product_type" id ="product_type">
								<option value=""></option>
								<?php
									$q = "SELECT * FROM product_type";
									$r = mysql_query($q, $conn);
									while($row = mysql_fetch_assoc($r)) {
								?>
								<option value="<?php echo $row['product_type']; ?>" <?php echo ($product['product_type'] == $row['product_type']) ? "selected" : ""; ?>><?php echo $row['name']; ?></option>
								<?php
									}
								?>
							</select>
						</td>
						<td colspan="2" style="text-align: right;"><input type="submit" name="submitButton" value="Update" style="width:100px;" /></td>
					</tr>
					<tr>
						<td colspan="2">
							Date:&nbsp;&nbsp;
							<input type="text" name="timestamp" id="timestamp" size="10" value="<?php echo $product['date']; ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							Product Description:<br />
							<textarea name="product_desc" id="product_desc" cols="35" rows="4"><?php echo $product['description']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4"><hr /></td>
					</tr>
					<tr>
						<td colspan="2">
							<a href="#" id="new_row">New Row</a>&nbsp;&nbsp;|&nbsp;&nbsp;
							<a href="#" id="del_row">Remove Row</a>
						</td>
					</tr>
					<tr style="text-align:left;">
						<th>Quantity</th>
						<th>Unit Price</th>
						<th>Extended Price</th>
					</tr>
					<?php
						$q = "SELECT * FROM prices WHERE product_id = " . mysql_real_escape_string($_GET['id']);
						$r = mysql_query($q, $conn);
						while($row = mysql_fetch_assoc($r)) {
					?>
					<tr>
						<td>
							<input class="integer quantity" type="text" name="quantity[]" size="10" value="<?php echo $row['quantity']; ?>" />
						</td>
						<td>$&nbsp;<input class="numeric unit_price" type="text" name="unit_price[]" size="10" value="<?php echo $row['unit_price']; ?>" /></td>
						<td>$&nbsp;<input class="numeric" type="text" name="extended_price[]" size="10" value="<?php echo $row['extended_price']; ?>" /></td>
					</tr>
					<?php
						}
					?>
				</table>
			</form>
		</div>
	</body>
</html>