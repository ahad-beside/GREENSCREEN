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
	if(isset($_POST['submitButton']) && isset($_POST['invoice_id']))
	{
		$ccn = $_POST['billing_ccn'];
		$addr = $_POST['billing_address'];
		$exp = $_POST['billing_exp'];
		$csc = $_POST['billing_csc'];
		$name = $_POST['billing_name'];
		$invoice_id = $_POST['invoice_id'];

			$q = "INSERT INTO billing_1099 (ccn, invoice_id, name, exp, address, csc) 
						VALUES('$ccn', '$invoice_id', '$name', '$exp', '$addr', '$csc')";
			$r = mysql_query($q, $conn);
	} else {
		$msg = "NO INVOICE ID SPECIFIED";
	}

	$rep_id = 0;
	$client = null;
	$err = "good";
	if(isset($_GET['add']))
	{
		$q = "SELECT * FROM billing_1099 WHERE invoice_id = " . mysql_real_escape_string($_GET['add']);
		$r = mysql_query($q, $conn);
		$client = mysql_fetch_assoc($r);
		$outp = <<<EOT
			<table cellpadding="3" cellspacing="3" style="margin:initial;">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"></b></td>
					</tr>
					
					<tr>
						<td class="right">NAME</td>
						<td>{$client['name']}</td>
					</tr>
					<tr>
						<td class="right">ADDRESS</td>
						<td>{$client['address']}</td>
					</tr>
					<tr>
						<td class="right">CARD NUMBER</td>
						<td>{$client['ccn']}<td>
					</tr>
					<tr>
						<td class="right">EXPIRATION (MM/YYYY)</td>
						<td>{$client['exp']}</td>
					</tr>
					<tr>
						<td valign="top" class="right">CSC</td>
						<td>{$client['csc']}</td>
					</tr>
					<tr>
					</tr>
				</table>
EOT;
		
		echo $outp;
	} else {
?>
		<script type="text/javascript">
			$(function() {
				$('#client').focus();
			});
		</script>
		<center><h3 id="msg"><?php echo $msg; ?><br><?php implode(' ', $msg2); ?></h3></center>
		<h2>Billing Information</h2>
		<form name="newInvoice" id="newInvoiceForm" method="post" action="">
			<input type="hidden" name="error" id="error" value="<?php echo $err; ?>" />
			<input type="hidden" name="invoice_id" id="error" value="<?php echo $_GET['id']; ?>" />
				<table cellpadding="3" cellspacing="3" style="margin:initial;">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"></b></td>
					</tr>
					
					<tr>
						<td class="right">NAME</td>
						<td><input class="required" type="text" name="billing_name" id="billing_name" size="12" /></td>
					</tr>
					<tr>
						<td class="right">ADDRESS</td>
						<td><textarea name="billing_address" id="billing_address" cols="30" rows="5"></textarea></td>
					</tr>
					<tr>
						<td class="right">CARD NUMBER</td>
						<td><input type="text" name="billing_ccn" id="billing_ccn" size="12" /><td>
					</tr>
					<tr>
						<td class="right">EXPIRATION (MM/YYYY)</td>
						<td><input type="text" name="billing_exp" id="billing_exp" size="30" /></td>
					</tr>
					<tr>
						<td valign="top" class="right">CSC</td>
						<td><input type="text" name="billing_csc" id="billing_csc" size="30" /></td>
					</tr>
					<tr>
					</tr>
				</table>
					<td colspan="3" style="text-align:right;"><input type="submit" name="submitButton" id="btnCreateInvoice" value="Submit Order" style="width: 100px;" /></td>
			</form>
		</div> <?php } ?>
	</body>
</html>