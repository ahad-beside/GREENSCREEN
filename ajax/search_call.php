<?php
	session_start();
	include('../config.php');
	 $limit = 30;
	 $offset = (isset($_POST['page']) and is_numeric($_POST['page'])) ? $_POST['page'] * $limit : 0;
	
	$q = "";
	$matches = array();

	if($_SESSION['level'] == '1') {
		$q = "SELECT * FROM leads WHERE `call` = '3'";
	}
	else {
		$q = "SELECT * FROM leads WHERE `call`='3' AND (`rep_id`='".$_POST['rep_id']."' OR `rep_id`='28')";
	}
	
	$r = mysql_query($q, $conn);
	while($row = mysql_fetch_assoc($r)) {
		$matches[] = $row;
	}
		
	$return = "";
	if(count($matches))
	{
		foreach($matches as $row)
			{
				$sales_q = "SELECT * FROM invoices WHERE lead_id = " . $row['lead_id'] . " ORDER BY purchase_date DESC";
				$sales_r = mysql_query($sales_q, $conn);
				
				$purchase_date = "-";
				$invoice_id = "-";
				if(mysql_num_rows($sales_r))
				{
					$rr = mysql_fetch_assoc($sales_r);
					$purchase_date = $rr['purchase_date'];
					$invoice_id = $rr['invoice_id'];
				}
				$return .= "<tr>\r\n";

				$return .= "<td><button type=\"button\" title=\"Remove From Call\" alt=\"Remove From Call\" class=\"edit_call\" id=\"$row[lead_id]\" name=\"edt_call\">Allow</button></td>\r\n";

				// $return .= "<td><a href=\"#\" type=\"button\" title=\"Remove From Sell\" alt=\"Remove From Sell\" class=\"edit_call\" id=\"$row[lead_id]\" name=\"edt_call\">Allow</a></td>\r\n";

				$return .= "<td><a href=\"history.php?id=$row[lead_id]\">$row[business]</a></td>\r\n";
				$return .= "<td>$row[contact]</td>\r\n";
				$return .= "<td>$row[address]</td>\r\n";
				$return .= "<td>$row[city]</td>\r\n";
				$return .= "<td>$row[state]</td>\r\n";
				$return .= "<td>$row[phone]</td>\r\n";
				$return .= "<td style=\"text-align:center;\">$purchase_date</td>\r\n";
				$return .= "<td style=\"text-align:center;\">$row[last_pulled]</td>\r\n";
				$return .= (($_SESSION['level'] == 1) OR ($_SESSION['level'] == 3))? "<td><a href=\"new_invoice.php?id=$row[lead_id]\">New Order</a></td></tr>\r\n" : "<td></td>";
				
				$return = stripslashes(convert_smart_quotes($return));
			}
		//}
	}
	
	echo $return;
	
	function convert_smart_quotes($string) 
	{
		$search = array(chr(145), 
						chr(146), 
						chr(147), 
						chr(148), 
						chr(151),
						chr(189)); 

		$replace = array("'", 
						 "'", 
						 '"', 
						 '"', 
						 '-',
						 '1/2'); 

		return str_replace($search, $replace, $string); 
	}
?>