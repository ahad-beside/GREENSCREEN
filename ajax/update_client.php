<?php
	session_start();
	include('../config.php');
	
	if($_SESSION['level'] != '1' AND $_SESSION['level'] != '3')
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	$msg = array();
	if(isset($_POST['business']))
	{
		$lead_id = mysql_real_escape_string($_POST['lead_id']);
		$contact = mysql_real_escape_string($_POST['contact']);
		$business = mysql_real_escape_string($_POST['business']);
		$address = mysql_real_escape_string($_POST['address']);
		$city = mysql_real_escape_string($_POST['city']);
		$state = mysql_real_escape_string($_POST['state']);
		$zipcode = mysql_real_escape_string($_POST['zipcode']);
		$phone1 = mysql_real_escape_string($_POST['phone1']);
		$phone2 = mysql_real_escape_string($_POST['phone2']);
		$phone3 = mysql_real_escape_string($_POST['phone3']);
		$rep_id = mysql_real_escape_string($_POST['rep']);
		$comment = mysql_real_escape_string($_POST['comment']);
		$email = mysql_real_escape_string($_POST['email']);
		
		$q = "UPDATE leads  SET  rep_id='$rep_id', contact='$contact', business='$business', address='$address', city='$city', state='$state', zipcode='$zipcode', phone='$phone1', fax='$phone2', phone3='$phone3', comment='$comment', email='$email' WHERE lead_id = $lead_id";
		mysql_query($q, $conn);
		
		if(!mysql_error())
		{
			$msg['success'] = true;
			$msg['msg'] = "Update Client Added";
		}
		else
		{
			$msg['success'] = false;
			$msg['msg'] = mysql_error();
		}
	}
	echo json_encode($msg);
?>