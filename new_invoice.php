<?php 
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php');
	
	if($_SESSION['level'] != '1' AND $_SESSION['level'] != '3')
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	$msg = "";
	if(isset($_POST['submitButton']))
	{
		$product_ids = $_POST['id'];
		$desc = $_POST['desc'];
		$quantities = $_POST['quantity'];
		$prices = $_POST['price'];
		$lead_id = mysql_real_escape_string($_POST['lead_id']);
		$date = mysql_real_escape_string($_POST['date']);
		$ship_date = mysql_real_escape_string($_POST['ship_date']);
		$paid_date = mysql_real_escape_string($_POST['paid_date']);
		$rep_id = mysql_real_escape_string($_POST['rep_id']);
		$dialer_id = mysql_real_escape_string($_POST['dialer_id']);
		$product_color = $_POST['product_color'];
		$imprint_color = $_POST['imprint_color'];
		$expense_desc = mysql_real_escape_string($_POST['expense_desc']);
		$expense_amt = mysql_real_escape_string($_POST['expense_amount']);
		
		if(count($product_ids) > 0 and $product_ids[0] != "" and $lead_id != "")
		{
			$q = "INSERT INTO invoices (lead_id, rep_id, dialer_id, purchase_date, ship_date, paid_date, expense_description, expense_amount) 
						VALUES($lead_id, '$rep_id', '$dialer_id', '$date', '$ship_date', '$paid_date', '$expense_desc', '$expense_amt')";
			$r = mysql_query($q, $conn);
			$invoice_id = mysql_insert_id();
			
			if($invoice_id)
			{
				$msg = "Invoice #" . $invoice_id;
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
		else
		{
			$msg = "Oops, did you choose a client or added any products?";
		}
	}
	
	$rep_id = 0;
	$client = null;
	$err = "good";
	if(isset($_GET['id']))
	{
		$q = "SELECT * FROM leads WHERE lead_id = " . mysql_real_escape_string($_GET['id']);
		$r = mysql_query($q, $conn);
		$client = mysql_fetch_assoc($r);
		$rep_id = $client['rep_id'];
		
		// Find any outstanding invoices for this lead
		$q = "SELECT * FROM invoices WHERE lead_id = " . mysql_real_escape_string($_GET['id']);
		$r = mysql_query($q, $conn);
		
		while($row = mysql_fetch_assoc($r))
		{
			if($row['paid_date'] == '0000-00-00')
			{
				$err = "error-paid";
				break;
			}
			else if($row['ship_date'] == '0000-00-00')
			{
				$err = "error-ship";
				break;
			}
		}
	}
?>
		<script type="text/javascript">
			$(function() {
				$('#client').focus();
			});
		</script>
		<center><h3 id="msg"><?php echo $msg; ?></h3></center>
		<form name="newInvoice" id="newInvoiceForm" method="post" action="">
			<input type="hidden" id="lead_id" name="lead_id" value="<?php echo (isset($_GET['id'])) ? $_GET['id'] : ""; ?>" />
			<input type="hidden" name="error" id="error" value="<?php echo $err; ?>" />
			<table cellpadding="3" cellspacing="3" style="width: 100%;">
				<tr>
					<td colspan="6" style="width:20px;">
						<hr />
						<b style="font-size: 20pt;">Order For:&nbsp;</b>
						<b>
							<?php 
							echo ($client != null) ? stripslashes($client['business'] . ' - ' . $client['address']) : ""; ?></b>
						<?php 
							if($client == null) {
						?>
						<input id="client" type="text" name="client" size="45" />&nbsp;&nbsp;
						<a id="modalForm" href="#">New Client</a>
						<?php } ?><br/><br />
						Address:&nbsp;&nbsp;<b id="addr" style="font-style:italic;"></b><br /> 
						Lead Owner:&nbsp;&nbsp;<b id="owner" style="font-style:italic;"></b>
						<hr />
					</td>
				</tr>
				<tr>
				</tr>
				<tr>
					<td>
						Purchase Date:&nbsp;&nbsp;
						<input type="text" name="date" size="10" value="<?php echo date('Y-m-d'); ?>" /><b style="font-size: 10px;">(YYYY-MM-DD)</b>
					</td>
					<td>
						Ship Date:&nbsp;&nbsp;
						<input type="text" name="ship_date" size="10" /><b style="font-size:10px;">(YYYY-MM-DD)</b>
					</td>
					<td>
						Paid Date:&nbsp;&nbsp;
						<input type="text" name="paid_date" size="10" /><b style="font-size:10px;">(YYYY-MM-DD)</b>
					</td>
				</tr>
			</table>
			<table id="items" cellpadding="3" cellspacing="3" style="width: 100%;">
				<tr>
					<td colspan="1">
						Lead:&nbsp;
						<select name="rep_id">
							<option value=""></option>
							<?php if($_SESSION['level'] == 1) { ?>
							<?php
								$q = "SELECT * FROM reps  ORDER BY rep_id ASC";						
								$r = mysql_query($q, $conn);
								while($row = mysql_fetch_assoc($r)) {
							?>
							<option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $rep_id) ? "selected" : "";?>><?php echo $row['rep_id']; ?></option>
							<?php } } ?>

							<?php
								$q = "SELECT * FROM `reps` WHERE `id` = ".$_SESSION['rep_id'];									
								$r = mysql_query($q, $conn);
								while($row = mysql_fetch_assoc($r)) {
							?>
							<option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $rep_id) ? "selected" : "";?>><?php echo $row['rep_id']; ?></option>
							<?php } ?>
						</select>
					</td>
					<td colspan="1">
						Dialer:&nbsp;
						<select name="dialer_id" <?php if($_SESSION['level'] <> 1) echo "disabled" ;?>>
							<option value=""></option
							<?php
								$q = "SELECT * FROM reps ORDER BY rep_id ASC";
								$r = mysql_query($q, $conn);
								while($row = mysql_fetch_assoc($r))
								{
							?>
							<option value="<?php echo $row['id']; ?>"><?php echo $row['rep_id']; ?></option>
							<?php } ?>	
						</select>
						
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<hr />
						<!--<a id="new_item" href="#">New Item</a>&nbsp;&nbsp;|&nbsp;&nbsp;
						<a id="remove_item" href="#">Remove Item</a>-->
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Copy Text:<br/ >
						<textarea id="desc" name="desc[]" cols="30" rows="6"><?php
							echo ($client != null) ? stripslashes($client['business'] . "\r\n" . $client['address'] . "\r\n" . $client['city'] . ", " . $client['state'] . " " . $client['zipcode'] . "\r\n" . $client['phone']) : "";
						?></textarea>
					</td>
				</tr>
				<tr>
					<td style="width:30%;">
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
				<tr>
					<td>
						Expense Description:<br />
						<input type="text" name="expense_desc" size="30" />
					</td>
					<td>
						Expense Amount:<br />
						$&nbsp;<input type="text" name="expense_amount" size="5" />
					</td>
					<td colspan="3" style="text-align:right;"><input type="submit" name="submitButton" id="btnCreateInvoice" value="Submit Order" style="width: 100px;" /></td>
				</tr>
			</table>
		</form>
		
		<div id="clientForm" style="display:none; width:600px;">
			<form name="newClient" id="modalForm" method="post" action="">
				<h2 style="text-align:center;">New Clientssss</h2>
				<table cellpadding="3" cellspacing="3" style="margin:auto;">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"></b></td>
					</tr>
					<tr>
						<td class="right">*Date</td>
						<td><input class="required" type="text" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" size="10" /></td>
					</tr>
					<tr>
						<td class="right">*Contact</td>
						<td><input class="required" type="text" name="contact" id="contact" /></td>
					</tr>
					<tr>
						<td class="right">*Business</td>
						<td><input class="required" type="text" name="business" id="business" size="30" /></td>
					</tr>
					<tr>
						<td class="right">*Address</td>
						<td><input class="required" type="text" name="address" id="address" size="30" /></td>
					</tr>
					<tr>
						<td class="right">*City</td>
						<td><input class="required" type="text" name="city" id="city" /></td>
					</tr>
					<tr>
						<td class="right">*State</td>
						<td><input class="required" type="text" name="state" id="state" size="2" /></td>
					</tr>
					<tr>
						<td class="right">*Zipcode</td>
						<td><input class="required" type="text" name="zipcode" id="zipcode" size="5" /></td>
					</tr>
					<tr>
						<td class="right">*Phone #1</td>
						<td><input class="required" type="text" name="phone1" id="phone1" size="12" /></td>
					</tr>
					<tr>
						<td class="right">Phone #2</td>
						<td><input type="text" name="phone2" id="phone2" size="12" /></td>
					</tr>
					<tr>
						<td class="right">Phone #3</td>
						<td><input type="text" name="phone3" id="phone3" size="12" /><td>
					</tr>
					<tr>
						<td class="right">*Sales Rep</td>
						<td>
							<select name="rep" id="rep" class="required">
								<option value=""></option>
								<?php if($_SESSION['level'] == 1) { ?>
								<?php
									$q = "SELECT * FROM `reps`";							
									$r = mysql_query($q, $conn);
									while($row = mysql_fetch_assoc($r)) {
								?>
								<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
								<?php } } ?>

								<?php
									$q = "SELECT * FROM `reps` WHERE `id` = ".$_SESSION['rep_id'];									
									$r = mysql_query($q, $conn);
									while($row = mysql_fetch_assoc($r)) {
								?>
								<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="right">Email</td>
						<td><input type="text" name="email" id="email" size="30" /></td>
					</tr>
					<tr>
						<td valign="top" class="right">Comment</td>
						<td><textarea name="comment" id="comment" cols="30" rows="5"></textarea></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="button" id="newClientModal" name="submitButton" value="Create Client" />&nbsp;&nbsp;
						<input type="button" id="closeModal" name="cancelButton" value="Cancel" /></td>	
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>