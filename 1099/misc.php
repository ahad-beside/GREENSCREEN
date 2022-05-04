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
	
	$msg = "";
	if(isset($_POST['invoiceButton']))
	{
		$which_date = $_POST['which_date'];
		$date = mysql_real_escape_string($_POST['new_date']);
		$invoices = $_POST['invoices'];
		
		switch($which_date)
		{
			case 'paid':
				
				break;
			case 'ship':
				break;
			case 'both':
				break;
		}
	}
	else if(isset($_POST['switchBtn']))
	{
		$startDate = mysql_real_escape_string($_POST['start_date']);
		$currRep = mysql_real_escape_string($_POST['current_rep']);
		$newRep = mysql_real_escape_string($_POST['new_rep']);
		
		$q = "SELECT lead_id, business FROM leads WHERE rep_id = $currRep";
		$r = mysql_query($q, $conn);
		
		$toBeSwitched = array();
		while($row = mysql_fetch_assoc($r))
		{
			$qq = "SELECT purchase_date FROM invoices WHERE lead_id = $row[lead_id] ORDER BY purchase_date DESC";
			$rr = mysql_query($qq, $conn);
			
			if(mysql_num_rows($rr) == 0)
				$toBeSwitched[] = $row['lead_id'];//array($row['lead_id'], $row['business'], '0000-00-00');
			else
			{
				$row2 = mysql_fetch_assoc($rr);
				if($row2['purchase_date'] < $startDate)
					$toBeSwitched[] = $row['lead_id'];//array($row['lead_id'], $row['business'], $row2['purchase_date']);
			}
		}
		
		$q = "SELECT rep_id FROM reps WHERE id = $currRep";
		$r = mysql_fetch_assoc(mysql_query($q, $conn));
		$currRepId = $r['rep_id'];
		
		$q = "SELECT rep_id FROM reps WHERE id = $newRep";
		$r = mysql_fetch_assoc(mysql_query($q, $conn));
		$newRepId = $r['rep_id'];
		
		$msg = "Moved " . count($toBeSwitched) . " leads from $currRepId to $newRepId";
		for($i = 0; $i < count($toBeSwitched); $i++)
		{
			$q = "UPDATE leads SET rep_id = $newRep WHERE lead_id = $toBeSwitched[$i]";
			$r = mysql_query($q, $conn);
		}
	}
?>
		<div style="float:left;width:25%;">
			<ul>
				<li><a href="#" id="paid_invoices" class="misc">Paid Invoices</a></li>
				<li><a href="#" id="ship_invoices" class="misc">Shipped Invoices</a></li>
				<li><a href="#" id="something" class="misc">Move Leads</a></li>
			</ul>
		</div>
		
		<div style="float:right;width:75%;">
			<h3 id="phpmsg" style="text-align:center;"><?php echo $msg; ?></h3>
			<div id="invoice_update" style="display:none;">
				<h2 id="title" style="text-align:center;"></h2>
				<table id="invoiceSearch" class="table" cellpadding="3" cellspacing="3" style="width:100%; font-size:12px;">
					<tr>
						<td colspan="3">
							Filter:<br />
							<input type="text" id="invoiceNum" name="invoiceNum" />&nbsp;&nbsp;
							<input type="button" id="searchInvoice" value="Search" />
						</td>
						<td colspan="2">
							<br />
							<input type="button" id="invoiceBtn" value="Update Invoices" />
						</td>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<th>Invoice Number</th>
						<th>Business</th>
						<th>Purchase Date</th>
						<th>Purchase Total</th>
					</tr>
				</table>
			</div>
		
			<div id="switchDiv" style="display:none;">
				<form name="form" method="post" action="">
					<h2 style="text-align:center;">Move Leads</h2>
					<table cellspacing="5" cellpadding="5">
						<tr>
							<td style="text-align:right;">Starting Date:</td>
							<td><input type="text" name="start_date" size="8" /></td>
						</tr>
						<tr>
							<td style="text-align:right;">Current Sales Rep:</td>
							<td>
								<select name="current_rep">
									<option value=""></option>
									<?php
										$q = "SELECT * FROM reps ORDER BY rep_id ASC";
										$r = mysql_query($q, $conn);
										while($row = mysql_fetch_assoc($r)) {
									?>
									<option value="<?php echo $row['id']; ?>"><?php echo $row['rep_id']; ?></option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td style="text-align:right;">New Sales Rep:</td>
							<td>
								<select name="new_rep">
									<option value=""></option>
									<?php
										$q = "SELECT * FROM reps ORDER BY rep_id ASC";
										$r = mysql_query($q, $conn);
										while($row = mysql_fetch_assoc($r)) {
									?>
									<option value="<?php echo $row['id']; ?>"><?php echo $row['rep_id']; ?></option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center;"><input type="submit" name="switchBtn" value="Move Leads" /></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		
		<div id="modalInvoices" style="display:none; width:300px; text-align:center; padding: 20px 0px;">
			<b id="msg"></b><br /><br />
			<input type="hidden" id="which_date" name="which_date" value="paid" />
			<input type="text" id="new_date" name="new_date" value="<?php echo date('Y-m-d'); ?>" size="10" /><br /><br />
			<input type="button" id="gg" value="Enter" />&nbsp;&nbsp;
			<input type="button" id="closeModal" value="Cancel" />
		</div>
		
	</body>
</html>