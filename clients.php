<?php 
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
		
	include('config.php'); 
	include('header.php'); 
?>			
		<div style="padding-bottom: 50px;">
			<?php if(($_SESSION['level'] == 1) OR ($_SESSION['level'] == 3)) { ?>
			<a href="new_client.php">New Client</a> <br/><br/>
			<?php } ?>
			<table id="leads" class="table" cellpadding="3" cellspacing="3" style="width: 100%; font-size:12px;">
				<tr>
					<td colspan="10">
						
						Filter:<br />
						<input class="search_input" type="hidden" id="rep_id" name="rep_id" value="<?php echo $_SESSION['rep_id']; ?>">
						<input class="search_input" type="text" id="business" name="business" placeholder="Business" />&nbsp;&nbsp;
						<input class="search_input" type="text" id="phone" name="phone" placeholder="Phone" />&nbsp;&nbsp;
						<input type="button" id="search" value="Search" />					

						<div style="float: right;">
						<input type="button" id="callsearch" value="Show Delete List">
						<input type="button" id="sellsearch" value="Show Don't Sale/Sale List">
						</div>
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
					//$q = "SELECT * FROM leads WHERE rep_id IN 
					//		(SELECT rep_id FROM `reps` WHERE `id` = ".$_SESSION['rep_id'].") ORDER BY business ASC LIMIT 0, 30";
					//$q = "SELECT * FROM leads ORDER BY business ASC LIMIT 0, 30";
					
					$q= "SELECT * FROM leads WHERE rep_id = ".$_SESSION[rep_id]." OR rep_id = '28' ORDER BY business ASC LIMIT 0, 30";

					$r = mysql_query($q, $conn);

					while($row = mysql_fetch_assoc($r))
					{continue;
						$sales_q = "SELECT * FROM invoices where lead_id = " . $row['lead_id'] . " ORDER BY purchase_date DESC";

						echo $sales_q;
						//echo "Hell";
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