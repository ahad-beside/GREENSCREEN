<?php
	session_start(); 
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
		
	include('header.php');
	include('config.php');
	
	$lead_id = mysql_real_escape_string($_GET['id']);
	
	$q = "SELECT * FROM leads WHERE lead_id = $lead_id";
	$r = mysql_query($q, $conn);
	$lead = mysql_fetch_assoc($r);
?>
		<div id="content">
			<h2 style="text-align:center;">
				<?php echo "PURCHASE HISTORY"; ?>
				<h3 style="text-align:center;"><?php echo stripslashes($lead['business']); ?></h3>
			</h2>
			<table id="invoices" class="table" cellpadding="3" cellspacing="3" style="width: 100%; text-align:center; border: 1px solid #fff;" >
				<tr>
					<th></th>
					<th>Invoice</th>
					<th>Purchase Date</th>
					<th>Ship Date</th>
					<th>Paid Date</th>
					<th>Purchase Total</th>
				</tr>
				<?php
					$q = "SELECT * FROM invoices WHERE lead_id = $lead_id ORDER BY purchase_date DESC";
					$r = mysql_query($q, $conn);
					while($row = mysql_fetch_assoc($r)) {
						$q2 = "SELECT price FROM items WHERE invoice_id=$row[invoice_id]";
						$r2 = mysql_query($q2, $conn);
						
						$total = 0.0;
						while($row2 = mysql_fetch_assoc($r2))
							$total += $row2['price'];
				?>
				<tr>
					<td><a href="#" class="delete_invoice" id="<?php echo $row['invoice_id']; ?>">Delete</a></td>
					<td><a href="edit_invoice.php?id=<?php echo $row['invoice_id']; ?>"><?php echo $row['invoice_id']; ?></a></td>
					<td><?php echo $row['purchase_date']; ?></td>
					<td><?php echo ($row['ship_date'] == "0000-00-00") ? "-" : $row['ship_date']; ?></td>
					<td><?php echo ($row['paid_date'] == "0000-00-00") ? "-" : $row['paid_date']; ?></td>
					<td><?php echo "$" . number_format($total, 2); ?></td>
				</tr>
				<?php
					}
				?>
			</table>
		</div>
	</body>
</html>