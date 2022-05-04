<?php 
	session_start();	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	 
	include('../config.php');
	
	$q = "UPDATE leads SET `marks_data` = '0000-00-00' WHERE `lead_id` = ".$_POST['id'];
	$r = mysql_query($q, $conn);
	return;	

?>