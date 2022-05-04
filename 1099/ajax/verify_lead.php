<?php
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('../config.php');
	
	if(isset($_GET['lead_id']) and is_numeric($_GET['lead_id']))
	{
		$q = "SELECT * FROM invoices WHERE lead_id = " . $_GET['lead_id'];
		$r = mysql_query($q, $conn);
		
		$msg = "good";
		while($row = mysql_fetch_assoc($r))
		{
			if($row['paid_date'] == '0000-00-00')
			{
				$msg = "error-paid";
				break;
			}
			else if($row['ship_date'] == '0000-00-00')
			{
				$msg = "error-ship";
				break;
			}
		}
		echo $msg;
	}
?>