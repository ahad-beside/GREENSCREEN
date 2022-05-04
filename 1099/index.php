<?php 
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php');
	
	if($_SESSION['level'] != 1)
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	$msg = "";$msg2 = "";
	if(isset($_POST['submitButton']))
	{
		$product_ids = $_POST['id'];
		$desc = $_POST['desc'];
		$quantities = $_POST['quantity'];
		$prices = $_POST['price'];
		// $lead_id = mysql_real_escape_string($_POST['lead_id']);
		$date = mysql_real_escape_string($_POST['date']);
		$ship_date = mysql_real_escape_string($_POST['ship_date']);
		$paid_date = mysql_real_escape_string($_POST['paid_date']);
		// $rep_id = mysql_real_escape_string($_POST['rep_id']);
		// $dialer_id = mysql_real_escape_string($_POST['dialer_id']);
		$product_color = $_POST['product_color'];
		$imprint_color = $_POST['imprint_color'];
		$expense_desc = mysql_real_escape_string($_POST['expense_desc']);
		$expense_amt = mysql_real_escape_string($_POST['expense_amount']);
		
		if(count($product_ids) > 0 and $product_ids[0] != "")
		{
			$q = "INSERT INTO invoices_1099 (purchase_date, ship_date, paid_date, expense_description, expense_amount) 
						VALUES('$date', '$ship_date', '$paid_date', '$expense_desc', '$expense_amt')";
			$r = mysql_query($q, $conn);
			$invoice_id = mysql_insert_id();
		}
		else
		{
			echo "<h1>FAILING</h1>";
			$msg = "Oops, did you choose a client or added any products?";
		}
	}
if(isset($_POST['business']))
	{
		$date = mysql_real_escape_string($_POST['date']);
		$contact = mysql_real_escape_string($_POST['contact']);
		$business = mysql_real_escape_string($_POST['business']);
		$address = mysql_real_escape_string($_POST['address']);
		$city = mysql_real_escape_string($_POST['city']);
		$state = mysql_real_escape_string($_POST['state']);
		$zipcode = mysql_real_escape_string($_POST['zipcode']);
		$phone1 = mysql_real_escape_string($_POST['phone1']);
		$phone2 = mysql_real_escape_string($_POST['phone2']);
		$phone3 = mysql_real_escape_string($_POST['phone3']);
		// $rep_id = mysql_real_escape_string($_POST['rep']);
		$comment = mysql_real_escape_string($_POST['comment']);
		$email = mysql_real_escape_string($_POST['email']);
		
		// Check if this client is already in the database
		$phone = preg_replace('/[\W\D]*/i', '', mysql_real_escape_string($phone1));
		
		$q = "SELECT * FROM leads_1099";
		$r = mysql_query($q, $conn);
		if (isset($invoice_id) && $invoice_id != '') {
			$q = "INSERT INTO leads_1099 (date, contact, business, address, city, state, zipcode, phone, fax, phone3, comment, email, invoice_id)
					VALUES('$date', '$contact', '$business', '$address', '$city', '$state', '$zipcode', '$phone1', '$phone2', '$phone3', '$comment', '$email', '$invoice_id')";
			mysql_query($q, $conn);
		} else {
			$q = "INSERT INTO leads_1099 (date, contact, business, address, city, state, zipcode, phone, fax, phone3, comment, email, invoice_id)
					VALUES('$date', '$contact', '$business', '$address', '$city', '$state', '$zipcode', '$phone1', '$phone2', '$phone3', '$comment', '$email', 0)";
			mysql_query($q, $conn);
		}
		if(!mysql_error())
		{
			$msg2['success'] = true;
			$msg2['msg'] = "New Client Added";
			$msg2['lead_id'] = mysql_insert_id();
			$msg2['ad_copy'] = $business . "\r\n" . $address . "\r\n" . $city . ", " . $state . " " . $zipcode . "\r\n". $phone1;
		}
		else
		{
			$msg2['success'] = false;
			$msg2['msg'] = mysql_error();
		}
	}
		
	$rep_id = 0;
	$client = null;
	$err = "good";
	if(isset($_GET['id']))
	{
		$q = "SELECT * FROM leads_1099 WHERE lead_id = " . mysql_real_escape_string($_GET['id']);
		$r = mysql_query($q, $conn);
		$client = mysql_fetch_assoc($r);
		$rep_id = $client['rep_id'];
		
		// Find any outstanding invoices for this lead
		$q = "SELECT * FROM invoices_1099 WHERE lead_id = " . mysql_real_escape_string($_GET['id']);
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
if (!empty($_POST))
{
	$data = <<<EOT
		CUSTOMER INFORMATION\r\n
		======================================\r\n
		Date: {$date}\r\n
		Contact: {$contact}\r\n
		Business: {$business}\r\n
		Address: {$address}\r\n
		City: {$city}\r\n
		State: {$state}\r\n
		Zipcode: {$zipcode}\r\n
		Phone1: {$phone1}\r\n
		Phone2: {$phone2}\r\n
		Phone3: {$phone3}\r\n
		Comment: {$comment}\r\n
		Email: {$email}\r\n\r\n
		ORDER INFORMATION\r\n
		======================================\r\n
		Product ID(s): {$product_ids}\r\n
		Desc: {$desc}\r\n
		Quantities: {$quantities}\r\n
		Prices: {$prices}\r\n
		Date: {$date}\r\n
		Ship date: {$ship_date}\r\n
		Paid date: {$paid_date}\r\n
		Product color: {$product_color}\r\n
		Imprint color: {$imprint_color}\r\n
		Expense desc: {$expense_desc}\r\n
		Expense amt: {$expense_amt}\r\n
EOT;
	mail('cheri@g3graphics.net', 'NEW INVOICE - G3GRAPHICS 1099', $data);
}
?>
		<script type="text/javascript">
			$(function() {
				$('#client').focus();
			});
		</script>
		<center><h3 id="msg"><?php echo $msg; ?><br><?php implode(' ', $msg2); ?></h3></center>
		<h2>Order Information</h2>
		<form name="newInvoice" id="newInvoiceForm" method="post" action="">
			<input type="hidden" name="error" id="error" value="<?php echo $err; ?>" />
			<table cellpadding="3" cellspacing="3" style="width: 100%;">
				<tr>

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
					
				</tr>
			</table>
		
		<div id="clientForm" style="width:600px;">
				<h2 style="">Client Information</h2>
				<table cellpadding="3" cellspacing="3" style="margin:initial;">
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
						<td class="right">Email</td>
						<td><input type="text" name="email" id="email" size="30" /></td>
					</tr>
					<tr>
						<td valign="top" class="right">Comment</td>
						<td><textarea name="comment" id="comment" cols="30" rows="5"></textarea></td>
					</tr>
					<tr>
					</tr>
				</table>
<td colspan="3" style="text-align:right;"><input type="submit" name="submitButton" id="btnCreateInvoice" value="Submit Order" style="width: 100px;" /></td>
			</form>
		</div>
	</body>
</html>