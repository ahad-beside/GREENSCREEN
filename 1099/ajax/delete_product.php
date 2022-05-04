<?php
	session_start();
	
	include('../config.php');
	
	if($_SESSION['level'] != 1)
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	$product_id = mysql_real_escape_string($_POST['id']);
	
	if(!empty($product_id) and is_numeric($product_id))
	{
		$q = "UPDATE products SET active = 0 WHERE product_id = " . $product_id;
		mysql_query($q, $conn);
	}
?>