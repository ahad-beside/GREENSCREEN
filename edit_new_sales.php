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
	if(isset($_POST['submitButton']))
	{
		$pass = mysql_real_escape_string($_POST['pass']);
		$password =md5($pass);
		$hob=$_POST['arr'];

		$q = "UPDATE users SET password='$password', access='$hob' WHERE user_id = ".$_GET['id'];
		//echo $q;
		mysql_query($q, $conn);
		
		$err = mysql_error();
		if(stristr($err, "Duplicate entry"))
			$msg = "A sales rep already has this ID";
		else
			$msg = "Sales rep information updated";
			header('Location:view_sales_user.php');
	}
	
	$q = "SELECT * FROM users LEFT JOIN reps on users.rep_id=reps.id WHERE users.user_id = ".$_GET['id'];
	$r = mysql_query($q, $conn);
	$rep = mysql_fetch_assoc($r);
?>
		<div id="content">
			<form name="newRep" id="newRep" method="post" action="">
				<h2 style="text-align:center;">Edit New Sales Rep User</h2>
				<input type="hidden" name="id" value="<?php echo $rep['id']; ?>" />
				<table cellpadding="3" cellspacing="5">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"><?php echo $msg; ?></b></td>
					</tr>
					<tr>
						<td class="right">User Name</td>
						<td>
							<input class="required" type="text" name="uname" value="<?php echo $rep['username']; ?>" readonly  />
						</td>
					</tr>
					<tr>
						<td class="right">Password</td>
						<td><input class="required" type="Password" name="pass" value="<?php echo $rep['password']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Sales Rep</td>
						<td>
							<select name="rep" id="rep" class="required">
							<option value="<?php echo $rep['id']; ?>"><?php echo $rep['name']; ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="right">House Account</td>
						<?php 
							$chkbox=$rep['access'];
							$arr[]=$chkbox;					
						?>
						<td><input 
						<?php 
							if(in_array("1",$arr)){echo "checked='checked'";}
						?>
							type="checkbox" name="arr" value="1"/>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="submit" name="submitButton" value="Update Rep" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>