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
	
	$msg = "";
	if(isset($_POST['submitButton']))
	{
		$product_code = mysql_real_escape_string($_POST['product_code']);
		$product_type = mysql_real_escape_string($_POST['product_type']);
		$product_desc = mysql_real_escape_string($_POST['product_desc']);
		$timestamp = mysql_real_escape_string($_POST['timestamp']);
		$quantity = $_POST['quantity'];
		$unit_price = $_POST['unit_price'];
		$extended_price = $_POST['extended_price'];
		
		if(!empty($product_code) and !empty($product_type))
		{
			$q = "INSERT INTO products (product_code, product_type, description, timestamp) VALUES('$product_code', '$product_type', '$product_desc', '$timestamp')";
			$r = mysql_query($q, $conn);
			
			$product_id = mysql_insert_id();
			if($product_id)
			{
				for($i = 0; $i < count($quantity); $i++)
				{
					$num = mysql_real_escape_string($quantity[$i]);
					$price = mysql_real_escape_string($unit_price[$i]);
					$extended = mysql_real_escape_string($extended_price[$i]);
					
					$q = "INSERT INTO prices (product_id, quantity, unit_price, extended_price)
							VALUES('$product_id', '$num', '$price', '$extended')";
					mysql_query($q, $conn);
				}
				
				$msg = "New product created!";
			}
			else
			{
				if(strstr(mysql_error(), 'Duplicate'))
					$msg = "Whoa, that product already exists";
			}
		}
	}
?>
		<div id="content" style="text-align:center;">
			<form name="newProduct" id="newProduct" method="post" action="">
				<h2 style="text-align:center;">New Product</h2>
				<table id="new_product" cellpadding="5">
					<tr>
						<td colspan="3" style="text-align: center;"><b id="msg"><?php echo $msg; ?></b></td>
					</tr>
					<tr>
						<td colspan="2">
							Product Code:&nbsp;&nbsp;
							<input type="text" name="product_code" id="product_code" size="10" />&nbsp;&nbsp;&nbsp;&nbsp;
							
							Product Type:&nbsp;&nbsp;
							<select name="product_type" id="product_type">
								<option value=""></option>
								<?php
									$q = "SELECT * FROM product_type";
									$r = mysql_query($q, $conn);
									while($row = mysql_fetch_assoc($r)) {
								?>
								<option value="<?php echo $row['product_type']; ?>"><?php echo $row['name']; ?></option>
								<?php
									}
								?>
							</select>
						</td>
						<td colspan="2" style="text-align: right;"><input type="submit" name="submitButton" value="Create" style="width:100px;" /></td>
					</tr>
					<tr>
						<td colspan="2">
							Date:&nbsp;&nbsp;
							<input type="text" name="timestamp" id="timestamp" size="10" value="<?php echo date('Y-m-d'); ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							Product Description:<br />
							<textarea name="product_desc" id="product_desc" cols="35" rows="4"></textarea>
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
					<tr>
						<td><input class="integer quantity" type="text" name="quantity[]" size="10" /></td>
						<td>$&nbsp;<input class="numeric unit_price" type="text" name="unit_price[]" size="10" /></td>
						<td>$&nbsp;<input class="numeric" type="text" name="extended_price[]" size="10" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>