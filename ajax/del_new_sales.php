<?php 
 
 	session_start();	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('../config.php');

	$q = "DELETE FROM `users` WHERE user_id = ".$_POST['id'];
	$r = mysql_query($q, $conn);
	// echo "<script type='text/javascript'>alert('Successfully Delete !');</script>";
	return;	
?>