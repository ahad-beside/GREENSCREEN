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
		$rep_id = mysql_real_escape_string($_POST['rep_id']);
		$name = mysql_real_escape_string($_POST['name']);
		$front = mysql_real_escape_string($_POST['front_comm']);
		$reload = mysql_real_escape_string($_POST['reload_comm']);
		$bonus_lvl = mysql_real_escape_string($_POST['bonus_lvl']);
		$bonus_per = mysql_real_escape_string($_POST['bonus_per']);
		$below_par = mysql_real_escape_string($_POST['below_par']);
		
		$q = "INSERT INTO reps (rep_id, name, front_comm, reload_comm, bonus_level, bonus_percentage, below_par)
				VALUES('$rep_id', '$name', '$front', '$reload', '$bonus_lvl', '$bonus_per', '$below_par')";
		mysql_query($q, $conn);
		
		$err = mysql_error();
		if(stristr($err, "Duplicate entry"))
			$msg = "A sales rep already has this ID";
		else {
			$msg = "New Sales rep added";
			if(empty($rep_id)) {
				$q = "UPDATE reps SET rep_id = '" . mysql_insert_id() . "' WHERE id = " . mysql_insert_id();
				mysql_query($q, $conn);
			}
		}
	}
?>
		<div id="content">
			<form name="newRep" id="newRep" method="post" action="">
				<h2 style="text-align:center;">New Sales Rep</h2>
				<table cellpadding="3" cellspacing="5">
					<tr>
						<td colspan="2" style="text-align:center;"><b id="msg"><?php echo $msg; ?></b></td>
					</tr>
					<tr>
						<td class="right">Rep ID</td>
						<td>
							<input type="text" name="rep_id" size="5" /><br />
							<b style="font-size:11px;">*Leave blank to auto generate</b>
						</td>
					</tr>
					<tr>
						<td class="right">*Name</td>
						<td><input class="required" type="text" name="name" /></td>
					</tr>
					<tr>
						<td class="right">*Front Commission</td>
						<td><input class="required" type="text" name="front_comm" size="5" /></td>
					</tr>
					<tr>
						<td class="right">*Reload Commision</td>
						<td><input class="required" type="text" name="reload_comm" size="5" /></td>
					</tr>
					<tr>
						<td class="right">*Bonus Level</td>
						<td><input class="required" type="text" name="bonus_lvl" size="5" /></td>
					</tr>
					<tr>
						<td class="right">*Bonus Percentage</td>
						<td><input class="required" type="text" name="bonus_per" size="5" /></td>
					</tr>
					<tr>
						<td class="right">*Below Par</td>
						<td><input class="required" type="text" name="below_par" size="5" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="submit" name="submitButton" value="Create Rep" /></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>