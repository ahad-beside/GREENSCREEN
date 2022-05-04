<?php
	include('../config.php');
	
	$zipcode = mysql_real_escape_string($_POST['zipcode']);
	
	$q = "SELECT * FROM zipcodes WHERE zipcode = '$zipcode'";
	$r = mysql_query($q, $conn);
	
	if(mysql_num_rows($r))
	{
		$row = mysql_fetch_assoc($r);
		echo $row['city'] . ',' . $row['state'];
	}
	else
		echo "";
?>