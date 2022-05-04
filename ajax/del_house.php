<?php 

 	session_start();	
	if(!isset($_SESSION['user']))
		header('Location: login.php');	
	include('../config.php');

	$q = "UPDATE leads SET `call` = '3' WHERE lead_id = ".$_POST['id'];
	mysql_query($q, $conn);
	
	$nextsql = "SELECT lead_id from leads where lead_id>". $_POST['id'] ." and leads.call != '3' AND (leads.`marks_data` = '0000-00-00' OR leads.`marks_data` <= '$currentDate') AND leads.rep_id = '28' ORDER BY `lead_id` ASC limit 1";
    $resultn = mysql_query($nextsql, $conn);
    $fetchn = mysql_fetch_array($resultn);
    echo $fetchn['lead_id'];
    exit;

?>