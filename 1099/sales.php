<?php 
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php'); 
	
	if(isset($_POST['submitButton']))
	{
		$product_ids = $_POST['id'];
		$desc = $_POST['desc'];
		$quantities = $_POST['quantity'];
		$prices = $_POST['price'];
		$lead_id = mysql_real_escape_string($_POST['lead_id']);
		$date = mysql_real_escape_string($_POST['date']);
		$ship_date = mysql_real_escape_string($_POST['ship_date']);
		$rep_id = mysql_real_escape_string($_POST['rep_id']);
		$dialer_id = mysql_real_escape_string($_POST['dialer_id']);
		$product_color = $_POST['product_color'];
		$imprint_color = $_POST['imprint_color'];
		
		if(count($product_ids))
		{
			$q = "INSERT INTO invoices (lead_id, rep_id, dialer_id, purchase_date, ship_date) VALUES($lead_id, '$rep_id', '$dialer_id', '$date', '$ship_date')";
			$r = mysql_query($q, $conn);
			$invoice_id = mysql_insert_id();
			
			if($invoice_id)
			{
				$q = "UPDATE leads SET last_pulled = '$date' WHERE lead_id = $lead_id";
				
				for($i = 0; $i < count($product_ids); $i++)
				{
					$id = mysql_real_escape_string($product_ids[$i]);
					$text = mysql_real_escape_string($desc[$i]);
					$prod_color = mysql_real_escape_string($product_color[$i]);
					$imprint = mysql_real_escape_string($imprint_color[$i]);
					$quantity = mysql_real_escape_string($quantities[$i]);
					$price = mysql_real_escape_string($prices[$i]);
					
					$q = "INSERT INTO items (invoice_id, product_id, ad_copy, product_color, imprint_color, quantity, price) 
							VALUES($invoice_id, '$id', '$text', '$prod_color', '$imprint', $quantity, '$price')";
					mysql_query($q, $conn);
				}
			}
		}
	}
	
	$q = "SELECT * FROM leads WHERE lead_id = " . mysql_real_escape_string($_GET['id']);
	$r = mysql_query($q, $conn);
	$client = mysql_fetch_assoc($r);
	$rep_id = $client['rep_id'];
?>
		<form name="newInvoice" method="post" action="">
			<input type="hidden" name="lead_id" value="<?php echo $_GET['id']; ?>" />
			<table id="items" cellpadding="3" cellspacing="3" style="width: 100%;">
				<tr>
					<td colspan="2" style="text-align:left;">
						Purchase Date:&nbsp;&nbsp;
						<input type="text" name="date" size="10" value="<?php echo date('Y-m-d'); ?>" /><b style="font-size: 10px;">(YYYY-MM-DD)</b>
					</td>
					<td colspan="2" style="text-align:left;">
						Ship Date:&nbsp;&nbsp;
						<input type="text" name="ship_date" size="10" /><b style="font-size:10px;">(YYYY-MM-DD)</b>
					</td>
					<td colspan="2" style="text-align:right;"><input type="submit" name="submitButton" value="Submit Order" style="width: 100px;" /></td>
				</tr>
				<tr>
					<td colspan="2">
						Lead:&nbsp;
						<select name="rep_id">
							<option value=""></option>
							<?php
								$q = "SELECT * FROM reps ORDER BY rep_id ASC";
								$r = mysql_query($q, $conn);
								while($row = mysql_fetch_assoc($r))
								{
							?>
							<option value="<?php echo $row['rep_id']; ?>" <?php echo ($row['rep_id'] == $rep_id) ? "selected" : "";?>><?php echo $row['rep_id']; ?></option>
							<?php
								}
							?>
						</select>
					</td>
					<td colspan="2">
						Dialer:&nbsp;
						<select name="dialer_id">
							<option value=""></option
							<?php
								$q = "SELECT * FROM reps ORDER BY rep_id ASC";
								$r = mysql_query($q, $conn);
								while($row = mysql_fetch_assoc($r))
								{
							?>
							<option value="<?php echo $row['rep_id']; ?>"><?php echo $row['rep_id']; ?></option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="6" style="width:20px;">
						<hr />
						<b style="font-size: 20pt;">Order For:&nbsp;</b><b><?php echo $client['business'] . ' - ' . $client['address']; ?></b>
						<hr />
						<a id="new_item" href="#">New Item</a>&nbsp;&nbsp;|&nbsp;&nbsp;
						<a id="remove_item" href="#">Remove Item</a>
					</td>
				</tr>
				<tr>
					<td>
						Product Code:<br />
						<select name="id[]">
							<option value=""></option>
							<?php	
								$q = "SELECT * FROM products INNER JOIN product_type USING(product_type) ORDER BY name ASC";
								$r = mysql_query($q, $conn);
								while($row = mysql_fetch_assoc($r))
								{
							?>
							<option value="<?php echo $row['product_id']; ?>"><?php echo $row['product_code'] . " - " . $row['name']; ?></option>
							<?php
								}
							?>
						</select>
					</td>
					<td>
						Copy Text:<br/ >
						<textarea name="desc[]" cols="30" rows="6"><?php
							echo $client['business'] . "\r\n" . $client['address'] . "\r\n" . $client['city'] . ", " . $client['state'] . " " . $client['zipcode'] . "\r\n" . $client['phone'];
						?></textarea>
					</td>
					<td>
						Product Color:<br />
						<input type="text" name="product_color[]" size="10" />
					</td>
					<td>
						Imprint Color:<br />
						<input type="text" name="imprint_color[]" size="10" />
					</td>
					<td>
						Quantity:<br />
						<input type="text" name="quantity[]" size="5" />
					</td>
					<td>
						Price:<br />
						$&nbsp;<input type="text" name="price[]" size="5" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>