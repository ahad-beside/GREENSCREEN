<?php 
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php'); 
	
	$msg = "";
	if(isset($_POST['submitButton']))
	{
		$product_ids = $_POST['id'];
		$desc = $_POST['desc'];
		$quantities = $_POST['quantity'];
		$prices = $_POST['price'];
		$invoice_id = mysql_real_escape_string($_POST['invoice_id']);
		$date = mysql_real_escape_string($_POST['date']);
		$ship_date = mysql_real_escape_string($_POST['ship_date']);
		$paid_date = mysql_real_escape_string($_POST['paid_date']);
		$rep_id = mysql_real_escape_string($_POST['rep_id']);
		$dialer_id = mysql_real_escape_string($_POST['dialer_id']);
		$product_color = $_POST['product_color'];
		$imprint_color = $_POST['imprint_color'];
		
		if(count($product_ids))
		{
			$q = "UPDATE invoices SET dialer_id='$dialer_id', purchase_date='$date', ship_date='$ship_date', paid_date='$paid_date' WHERE invoice_id=$invoice_id";
			$r = mysql_query($q, $conn);
			
			// Remove all items associated with this invoice
			$q = "DELETE FROM items WHERE invoice_id = $invoice_id";
			mysql_query($q, $conn);
			
			// Insert new items for this invoice
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
				
				if(!mysql_error())
					$msg = "Invoice has been updated";
				else
					$msg = "An error occurred: " . mysql_error();
			}
		}
	}
	
	$invoice_id = mysql_real_escape_string($_GET['id']);
	$q = "SELECT leads.*, invoices.*, invoices.rep_id AS seller_id, reps.rep_id AS owner FROM reps, invoices INNER JOIN leads USING(lead_id) WHERE invoice_id = " . $invoice_id . " AND reps.id = leads.rep_id";
	$r = mysql_query($q, $conn);
	$invoice = mysql_fetch_assoc($r);
?>
		<h3 style="text-align:center;"><?php echo $msg; ?></h3>
		<form name="newInvoice" method="post" action="">
			<input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>" />
			<table cellpadding="3" cellspacing="3" style="width: 100%;">
				<?php if($_SESSION['level'] == 1) { ?>
				<tr>
					<td colspan="3" style="text-align:right;"><input type="submit" name="submitButton" value="Update Order" style="width: 100px;" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="6" style="width:20px;">
						<hr />
						<b style="font-size: 20pt;">Order For:&nbsp;</b><b><?php echo $invoice['business'] . ' - ' . stripslashes($invoice['address']); ?></b>
						<br /><br />
						Lead Owner:&nbsp;&nbsp;<b style="font-style:italic;"><?php echo $invoice['owner']; ?></b>
						<hr />
					</td>
				</tr>
				<tr>
					<td>
						Purchase Date:&nbsp;&nbsp;
						<input type="text" name="date" size="10" value="<?php echo $invoice['purchase_date']; ?>" /><b style="font-size: 10px;">(YYYY-MM-DD)</b>
					</td>
					<td>
						Ship Date:&nbsp;&nbsp;
						<input type="text" name="ship_date" size="10" value="<?php echo ($invoice['ship_date'] == "0000-00-00") ? "" : $invoice['ship_date']; ?>" /><b style="font-size:10px;">(YYYY-MM-DD)</b>
					</td>
					<td>
						Paid Date:&nbsp;&nbsp;
						<input type="text" name="paid_date" size="10" value="<?php echo ($invoice['paid_date'] == "0000-00-00") ? "" : $invoice['paid_date']; ?>" /><b style="font-size:10px;">(YYYY-MM-DD)</b>
					</td>
				</tr>
			</table>
			<table id="items" cellpadding="3" cellspacing="3" style="width: 100%;">
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
							<option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $invoice['seller_id']) ? "selected" : "";?>><?php echo $row['rep_id']; ?></option>
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
							<option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $invoice['dialer_id']) ? "selected" : ""; ?>><?php echo $row['rep_id']; ?></option>
							<?php
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="6">
						<hr />
						<a id="new_item" href="#">New Item</a>&nbsp;&nbsp;|&nbsp;&nbsp;
						<a id="remove_item" href="#">Remove Item</a>
					</td>
				</tr>
				<?php
					$q = "SELECT * FROM items WHERE invoice_id = " . $invoice_id;
					$r = mysql_query($q, $conn);
					while($item = mysql_fetch_assoc($r)) {
				?>
				<tr>
					<td>
						Product Code:<br />
						<select name="id[]">
							<option value=""></option>
							<?php	
								$q2 = "SELECT * FROM products INNER JOIN product_type USING(product_type) ORDER BY name ASC";
								$r2 = mysql_query($q2, $conn);
								while($row = mysql_fetch_assoc($r2))
								{
							?>
							<option value="<?php echo $row['product_id']; ?>" <?php echo ($row['product_id'] == $item['product_id']) ? "selected" : ""; ?>><?php echo $row['product_code'] . " - " . stripslashes($row['name']); ?></option>
							<?php
								}
							?>
						</select>
					</td>
					<td>
						Copy Text:<br/ >
						<textarea name="desc[]" cols="30" rows="6"><?php
							echo stripslashes($item['ad_copy']);
						?></textarea>
					</td>
					<td>
						Product Color:<br />
						<input type="text" name="product_color[]" size="10" value="<?php echo stripslashes($item['product_color']); ?>" />
					</td>
					<td>
						Imprint Color:<br />
						<input type="text" name="imprint_color[]" size="10" value="<?php echo stripslashes($item['imprint_color']); ?>" />
					</td>
					<td>
						Quantity:<br />
						<input type="text" name="quantity[]" size="5" value="<?php echo $item['quantity']; ?>" />
					</td>
					<td>
						Price:<br />
						$&nbsp;<input type="text" name="price[]" size="5" value="<?php echo $item['price']; ?>" />
					</td>
				</tr>
				<?php
					}
				?>
			</table>
		</form>
	</body>
</html>