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
		$id = mysql_real_escape_string($_POST['id']);
		$new_rep_id = mysql_real_escape_string($_POST['new_rep_id']);
		$name = mysql_real_escape_string($_POST['name']);
		$front = mysql_real_escape_string($_POST['front_comm']);
		$reload = mysql_real_escape_string($_POST['reload_comm']);
		$bonus_lvl = mysql_real_escape_string($_POST['bonus_lvl']);
		$bonus_per = mysql_real_escape_string($_POST['bonus_per']);
		$below_par = mysql_real_escape_string($_POST['below_par']);
		
		$q = "UPDATE reps SET rep_id='$new_rep_id', name='$name', front_comm='$front', reload_comm='$reload', bonus_level='$bonus_lvl', bonus_percentage='$bonus_per', below_par='$below_par' WHERE id = $id";
		mysql_query($q, $conn);
		
		$err = mysql_error();
		if(stristr($err, "Duplicate entry"))
			$msg = "A sales rep already has this ID";
		else
			$msg = "Sales rep information updated";
	}
	
	$q = "SELECT * FROM reps WHERE id = " . mysql_real_escape_string($_GET['id']);
	$r = mysql_query($q, $conn);
	$rep = mysql_fetch_assoc($r);
?>
		<div id="content">
			<form name="newRep" id="newRep" method="post" action="">
				<h2 style="text-align:center;">Edit Sales Rep</h2>
				<input type="hidden" name="id" value="<?php echo $rep['id']; ?>" />
				<table cellpadding="3" cellspacing="5">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"><?php echo $msg; ?></b></td>
					</tr>
					<tr>
						<td class="right">Rep ID</td>
						<td>
							<input type="text" name="new_rep_id" size="5" value="<?php echo $rep['rep_id']; ?>" />
						</td>
					</tr>
					<tr>
						<td class="right">*Name</td>
						<td><input class="required" type="text" name="name" value="<?php echo $rep['name']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Front Commission</td>
						<td><input class="required" type="text" name="front_comm" size="5" value="<?php echo $rep['front_comm']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Reload Commision</td>
						<td><input class="required" type="text" name="reload_comm" size="5" value="<?php echo $rep['reload_comm']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Bonus Level</td>
						<td><input class="required" type="text" name="bonus_lvl" size="5"value="<?php echo $rep['bonus_level']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Bonus Percentage</td>
						<td><input class="required" type="text" name="bonus_per" size="5"value="<?php echo $rep['bonus_percentage']; ?>" /></td>
					</tr>
					<tr>
						<td class="right">*Below Par</td>
						<td><input class="required" type="text" name="below_par" size="5"value="<?php echo $rep['below_par']; ?>" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="submit" name="submitButton" value="Update Rep" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>