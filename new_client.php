<?php
	session_start();	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php');
	
	if(($_SESSION['level'] != 1) AND ($_SESSION['level'] != 3))
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	$msg = "";
	if(isset($_POST['submitButton']))
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
		$rep_id = mysql_real_escape_string($_POST['rep']);
		$comment = mysql_real_escape_string($_POST['comment']);
		$email = mysql_real_escape_string($_POST['email']);
		
		// Make sure this client isn't already in the database
		$q = "SELECT phone FROM leads";
		$r = mysql_query($q, $conn);
		
		$newRecord = true;
		while($row = mysql_fetch_assoc($r))
		{
			$thisPhone = preg_replace('/[\D]/', '', $row['phone']);
			$newPhone = preg_replace('/[\D]/', '', $phone1);
			
			if($thisPhone == $newPhone)
			{
				$newRecord = false;
				break;
			}
		}
		
		if($newRecord)
		{
			$q = "INSERT INTO leads (rep_id, date, contact, business, address, city, state, zipcode, phone, fax, phone3, comment, email)
					VALUES('$rep_id', '$date', '$contact', '$business', '$address', '$city', '$state', '$zipcode', '$phone1', '$phone2', '$phone3', '$comment', '$email')";
			mysql_query($q, $conn);
			
			if(!mysql_error())
				$msg = "New client added";
			else
				$msg = mysql_error();
		}
		else
			$msg = "Oops, this client already exists";
	}
?>
		<div id="content">
			<form name="newClient" id="newClient" method="post" action="">
				<h2 style="text-align:center;">New Client</h2>
				<table cellpadding="3" cellspacing="3" style="margin:auto;">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"><?php echo $msg; ?></b></td>
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
						<td colspan="2" style="text-align:center;"><input type="submit" name="submitButton" value="Create Client" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>