<?php
	session_start();
	include('../config.php');
	
	if($_SESSION['level'] != 1)
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	$msg = array();
	if(isset($_POST['business']))
	{
		$date = mysql_real_escape_string($_POST['date']);
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
		
		// Check if this client is already in the database
		$phone = preg_replace('/[\W\D]*/i', '', mysql_real_escape_string($phone1));
		
		$q = "SELECT * FROM leads";
		$r = mysql_query($q, $conn);
		
		while($row = mysql_fetch_assoc($r))
		{
			$rowPhone1 = preg_replace('/[\W\D]*/i', '', $row['phone']);
			
			if($rowPhone1 == $phone)
			{
				$msg['success'] = false;
				$msg['msg'] = "This client already exists";
				echo json_encode($msg);
				return;
			}
		}
		
		$q = "INSERT INTO leads (rep_id, date, contact, business, address, city, state, zipcode, phone, fax, phone3, comment, email)
				VALUES('$rep_id', '$date', '$contact', '$business', '$address', '$city', '$state', '$zipcode', '$phone1', '$phone2', '$phone3', '$comment', '$email')";
		mysql_query($q, $conn);
		
		if(!mysql_error())
		{
			$msg['success'] = true;
			$msg['msg'] = "New Client Added";
			$msg['lead_id'] = mysql_insert_id();
			$msg['ad_copy'] = $business . "\r\n" . $address . "\r\n" . $city . ", " . $state . " " . $zipcode . "\r\n". $phone1;
		}
		else
		{
			$msg['success'] = false;
			$msg['msg'] = mysql_error();
		}
	}
	echo json_encode($msg);
?>