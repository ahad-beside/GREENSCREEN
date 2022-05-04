<?php 
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
		
	include('config.php'); 
	include('header.php'); 
?>
		<div style="padding-bottom: 50px;">
			<?php if($_SESSION['level'] == 1) { ?>
			<a href="new_client.php">New Client</a><br /><br />
			<?php } ?>
			<table id="leads" class="table" cellpadding="3" cellspacing="3" style="width: 100%; font-size:12px;">
				<tr>
					<td colspan="10">
						Filter:<br />
						<input class="search_input" type="text" id="business" name="business" />&nbsp;&nbsp;
						<input class="search_input" type="text" id="phone" name="phone" />&nbsp;&nbsp;
						<input type="button" id="search" value="search" />
					</td>
				</tr>
				<tr>
					<th></th>
					<th>Business</th>
					<th>Contact</th>
					<th>Address</th>
					<th>City</th>
					<th>State</th>
					<th>Phone</th>
					<th>Last Order</th>
					<th>Last Pulled</th>
					<th></th>
				</tr>
				<?php
					$q = "SELECT * FROM leads ORDER BY business ASC LIMIT 0, 30";
					$r = mysql_query($q, $conn);
					
					while($row = mysql_fetch_assoc($r))
					{continue;
						$sales_q = "SELECT * FROM invoices where lead_id = " . $row['lead_id'] . " ORDER BY purchase_date DESC";
						$sales = mysql_query($sales_q, $conn);
						
						$purchase_date = "-";
						if(mysql_num_rows($sales))
						{
							$date = mysql_fetch_assoc($sales);
							$date = $date['purchase_date'];
						}
				?>
				<tr>
					<td><?php echo $row['business']; ?></td>
					<td><?php echo $row['address']; ?></td>
					<td><?php echo $purchase_date; ?></td>
				</tr>
				<?php
					}
				?>
			</table>
		</div>
	</body>
</html>