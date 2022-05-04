<?php
	include('../config.php');
	
	$limit = 30;
	$offset = (isset($_GET['page']) and is_numeric($_GET['page'])) ? $_GET['page'] * $limit : 0;
	
	$q = "";
	$matches = array();
	if(isset($_GET['query']) and !empty($_GET['query']))
	{
		$phone = preg_replace('/[\W\D]*/i', '', mysql_real_escape_string($_GET['query']));
		
		$q = "SELECT leads.*, reps.rep_id AS repid FROM leads INNER JOIN reps ON reps.id = leads.rep_id";
		$r = mysql_query($q, $conn);
		
		while($row = mysql_fetch_assoc($r))
		{
			$rowPhone1 = preg_replace('/[\W\D]*/i', '', $row['phone']);
			$rowPhone2 = preg_replace('/[\W\D]*/i', '', $row['fax']);
			$rowPhone3 = preg_replace('/[\W\D]*/i', '', $row['phone3']);
			
			if(stristr($rowPhone1, $phone) or stristr($rowPhone2, $phone) or stristr($rowPhone3, $phone))
			{
				$matches[] = $row;
			}
		}
	}
	
	$return = array();
	$return['query'] = $_GET['query'];
	if(count($matches)) 
	{
		$suggestions = array();
		$data = array();
		
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
			
			$suggestions[] = stripslashes(convert_smart_quotes($row['business'] . " - " . $row['phone']));
			
			$venue = stripslashes(convert_smart_quotes($row['business'] . "\r\n" . $row['address'] . "\r\n" . $row['city'] . ", " . $row['state'] . " " . $row['zipcode'] . "\r\n" . $row['phone']));
			$addr = stripslashes(convert_smart_quotes($row['address'] . "\r\n" . $row['city'] . ", " . $row['state'] . " " . $row['zipcode']));
			$row2 = array(
				'id'=>$row['lead_id'], 
				'venue'=>$venue,
				'owner'=>$row['repid'],
				'addr'=>$addr
			);
			$data[] = $row2;
		}
		
		$return['suggestions'] = $suggestions;
		$return['data'] = $data;
	}

	echo json_encode($return);
	
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