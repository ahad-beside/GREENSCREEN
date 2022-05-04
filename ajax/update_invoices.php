<?php
	include('../config.php');
	
	if(isset($_GET['which_date']) and isset($_GET['invoice_ids']))
	{
		$which_date = mysql_real_escape_string($_GET['which_date']);
		$new_date = mysql_real_escape_string($_GET['new_date']);
		$ids = mysql_real_escape_string($_GET['invoice_ids']);
		$ids = substr($ids, 0, -1);
		
		$q = "";
		switch($which_date)
		{
			case 'paid':
				$q = "UPDATE invoices SET paid_date = '$new_date' WHERE invoice_id IN ($ids)";
				break;
			case 'ship':
				$q = "UPDATE invoices SET ship_date = '$new_date' WHERE invoice_id IN ($ids)";
				break;
		}
		
		if($q != "")
		{
			$r = mysql_query($q, $conn);
			if(mysql_error())
			{
				echo "An error occurred: " . mysql_error();
			}
			else
			{
				echo "success";
			}
		}
	}
?>