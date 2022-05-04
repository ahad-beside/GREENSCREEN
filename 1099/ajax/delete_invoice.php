<?php
	session_start();
	
	include('../config.php');
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	if($_SESSION['level'] != 1)
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	if(isset($_GET['id']) and is_numeric($_GET['id']))
	{
		// Delete items associated with this invoice
		$q = "DELETE FROM items WHERE invoice_id = " . $_GET['id'];
		mysql_query($q, $conn);
		
		// Delete invoice itself
		$q = "DELETE FROM invoices WHERE invoice_id = " . $_GET['id'];
		mysql_query($q, $conn);
	}
?>