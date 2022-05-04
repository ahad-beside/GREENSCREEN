<?php  

 	session_start();	
	if(!isset($_SESSION['user']))
		header('Location: login.php');	
	include('../config.php');

	$currentDate = date("Y-m-d");
	$date30 = date('Y-m-d', strtotime("+30 day", strtotime($currentDate)));

	$q = "UPDATE leads SET `marks_data` =  '$date30' WHERE lead_id = ".$_POST['id'];
	mysql_query($q, $conn);

	$nextsql = "SELECT lead_id from leads where lead_id>". $_POST['id'] ." and leads.call != '3' AND (leads.`marks_data` = '0000-00-00' OR leads.`marks_data` <= '$currentDate') AND leads.rep_id = '28' order by lead_id ASC limit 1";
    $resultn = mysql_query($nextsql, $conn);
    $fetchn = mysql_fetch_array($resultn);
    echo $fetchn['lead_id'];
    exit;
?>