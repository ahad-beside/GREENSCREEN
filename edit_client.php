<?php
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php');
	
	$lead_id = mysql_real_escape_string($_GET['id']);
	$msg = "";
	if(isset($_POST['submitButton']))
	{
		$lead_id = mysql_real_escape_string($_POST['lead_id']);
		$date = mysql_real_escape_string($_POST['date']);
		$last_pulled = mysql_real_escape_string($_POST['last_pulled']);
		$contact = mysql_real_escape_string($_POST['contact']);
		$business = mysql_real_escape_string($_POST['business']);
		$address = mysql_real_escape_string($_POST['address']);
		$city = mysql_real_escape_string($_POST['city']);
		$state = mysql_real_escape_string($_POST['state']);
		$zipcode = mysql_real_escape_string($_POST['zipcode']);
		$phone1 = mysql_real_escape_string($_POST['phone1']);
		$phone2 = mysql_real_escape_string($_POST['phone2']);
		$phone3 = mysql_real_escape_string($_POST['phone3']);
		$rep_id = mysql_real_escape_string($_POST['rep']);
		$comment = mysql_real_escape_string($_POST['comment']);
		$email = mysql_real_escape_string($_POST['email']);
		
		$q = "UPDATE leads SET rep_id='$rep_id', date='$date', last_pulled='$last_pulled', contact='$contact', business='$business', address='$address', city='$city', state='$state', zipcode='$zipcode', phone='$phone1', fax='$phone2', phone3='$phone3', comment='$comment', email='$email' WHERE lead_id = $lead_id";
		mysql_query($q, $conn);echo mysql_error();
		$msg = "Client information updated";
	}
	
	$q = "SELECT * FROM leads WHERE lead_id = $lead_id";
	$r = mysql_query($q, $conn);
	$lead = mysql_fetch_assoc($r);
?>
		<div id="content">
			<form name="newClient" id="newClient" method="post" action="">
				<h2 style="text-align:center;">Edit Client</h2>
				<input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>" />
				<?php 
					//if($_SESSION['level'] != 1) 
					if(($_SESSION['level'] != 1) AND ($_SESSION['level'] != 3))	{ ?>
				<input type="hidden" name="last_pulled" value="<?php echo $lead['last_pulled']; ?>" />
				<?php } ?>
				<table cellpadding="3" cellspacing="3" style="margin:auto;">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"><?php echo $msg; ?></b></td>
					</tr>
					<tr>
						<td class="right">*Date</td>
						<td><input class="required" type="text" name="date" id="date" value="<?php echo $lead['date']; ?>" size="10" /></td>
					</tr>
					<?php 
						//if($_SESSION['level'] == 1) 
						if(($_SESSION['level'] != 1) AND ($_SESSION['level'] != 3)) { ?>
					<tr>
						<td class="right">Last Pulled</td>
						<td><input type="text" name="last_pulled" id="last_pulled" value="<?php echo $lead['last_pulled']; ?>" /></td>
					</tr>
					<?php } ?>
					<tr>
						<td class="right">*Contact</td>
						<td><input class="required" type="text" name="contact" id="contact" value="<?php echo stripslashes($lead['contact']); ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Business</td>
						<td><input class="required" type="text" name="business" id="business" size="30" value="<?php echo stripslashes($lead['business']); ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Address</td>
						<td><input class="required" type="text" name="address" id="address" size="30" value="<?php echo stripslashes($lead['address']); ?>" /></td>
					</tr>
					<tr>
						<td class="right">*City</td>
						<td><input class="required" type="text" name="city" id="city" value="<?php echo stripslashes($lead['city']); ?>" /></td>
					</tr>
					<tr>
						<td class="right">*State</td>
						<td><input class="required" type="text" name="state" id="state" size="2" value="<?php echo $lead['state']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Zipcode</td>
						<td><input class="required" type="text" name="zipcode" id="zipcode" size="5" value="<?php echo $lead['zipcode']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Phone #1</td>
						<td><input class="required" type="text" name="phone1" id="phone1" size="12" value="<?php echo $lead['phone']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">Phone #2</td>
						<td><input type="text" name="phone2" id="phone2" size="12" value="<?php echo $lead['fax']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">Phone #3</td>
						<td><input type="text" name="phone3" id="phone3" size="12" value="<?php echo $lead['phone3']; ?>" /><td>
					</tr>
					<tr>
						<td class="right">*Sales Rep</td>
						<td>
							<select name="rep" id="rep" class="required">
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
					</tr>
					<tr>
						<td class="right">Email</td>
						<td><input type="text" name="email" id="email" size="30" value="<?php echo $lead['email']; ?>" /></td>
					</tr>
					<tr>
						<td valign="top" class="right">Comment</td>
						<td><textarea name="comment" id="comment" cols="30" rows="5"><?php echo stripslashes($lead['comment']); ?></textarea></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="submit" name="submitButton" value="Update Client" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>