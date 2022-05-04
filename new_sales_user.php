<?php
	session_start();	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	include('config.php');
	include('header.php');
	if($_SESSION['level'] != 1)	{
		echo "<center><b>You don't have access to this</b></center>";
		return;	}
	$msg = "";
	if(isset($_POST['submitButton'])){
		$uname = mysql_real_escape_string($_POST['uname']);
		$pword = mysql_real_escape_string($_POST['pword']);
		//$level = mysql_real_escape_string($_SESSION['level']);
		$id = mysql_real_escape_string($_POST['rep']);
		$check = mysql_real_escape_string($_POST['check']);
		$pass = md5($pword);
			
		// Make sure this client isn't already in the database //" . $row['lead_id'] . "
		$q = "SELECT username, rep_id FROM users";
		$r = mysql_query($q, $conn);

		$newRecord = true;
		while($row = mysql_fetch_assoc($r))
		{
			$thisName = preg_replace('/[^a-zA-Z_\-0-9]/i', '', $row['username']);
			$newName = preg_replace('/[^a-zA-Z_\-0-9]/i', '', $uname);

			$thisRep = preg_replace('/[^0-9]/', '', $row['rep_id']);
			$newRep = preg_replace('/[^0-9]/', '', $id);

			if($thisName == $newName)  {
				$newRecord = false;
				break; 
			}
			if($thisRep == $newRep)  {
				$newRecord = false;
				break; 
			}
		}		
		if($newRecord){
			$q = "INSERT INTO users (username, password, user_level, rep_id, access)
					VALUES('$uname','$pass','3', '$id', '$check')";
			mysql_query($q, $conn);
			
			if(!mysql_error())
				$msg = "New client added";
				//header('Location:view_sales_user.php');
			else
				$msg = mysql_error(); 
		}
		else
			$msg = "Oops, this client already exists";	
	}
?>

<div>
<form name="newSalesUser" id="newSalesUser" method="post" action="">
<h2 style="text-align:center;">New Sales Rep User</h2>
<table cellpadding="3" cellspacing="3" style="margin:auto;">
	<tr>
		<td colspan="2" style="text-align:center;"><b id="msg"><?php echo $msg; ?></b></td>
	</tr>
	<tr>
		<td class="right">*User Name</td>
		<td><input class="required" type="text" name="uname" id="uname" required/></td>
	</tr>
	<tr>
		<td class="right">*Password</td>
		<td><input class="required" type="password" name="pword" id="password" size="30" /></td>
	</tr>
	<tr>
		<td class="right">*Confram Password</td>
		<td><input class="required" type="password" name="cpword" id="confirm_password" size="30" /><br/><span id='message'></span></td>
	</tr>
	<tr>
		<td class="right">*Sales Rep</td>
		<td>
		<select name="rep" id="rep" class="required">
		<option value=""></option>
		<?php
			$q = "SELECT * FROM reps";
			$r = mysql_query($q, $conn);
			while($row = mysql_fetch_assoc($r)) {
		?>
		<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
		<?php } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="right">House Account</td>
		<td><input type="checkbox" id="check" name="check" value="1"checked/></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;">
			<input type="submit" name="submitButton" value="Create Sales Rep" />
			<a href="reps.php">
			<input type="button" id="closeModal" name="cancelButton" value="Back" />
			</a>
		</td>
	</tr>
</table>
</form>
</div> 
<script type="text/javascript">
	
  $('#password, #confirm_password').on('keyup', function () {
  if ($('#password').val() == $('#confirm_password').val()) {
    $('#message').html('Matching').css('color', 'green');
  } else 
    $('#message').html('Not Matching').css('color', 'red');
});

</script>
</body>
</html>