<?php
/**************************************************
 *
 * Name: leads_rerun.php
 * Description: Pull leads report for a given date.
 * Date: 05/24/13
 * Author: Bryan Gutierrez
 *
 *************************************************/
 
 	include('./config.php');
 	require_once('./phpdocx/classes/CreateDocx.inc');
 	
 	session_start();
	if(!isset($_SESSION['user']))
		header('Location: login.php');
		
	$PAGE_LINES = 26;
	$SPACES = 120;
	$paramsText = array(
		'lineSpacing' => 480,
		'sz' => 9
	);
 	
 	/*$date = mysql_real_escape_string($_GET['date']);
 	
 	if(!isset($_GET['date']) || empty($_GET['date']))
 	{
 		echo "Please provide a proper date";
 		return;
 	}*/
 	
 	$leadCount = array();
 	$docx = new CreateDocx();
 				$q = 'SELECT instance FROM pull_track WHERE id = 1';
			$weeks = mysql_result(mysql_query($q), 0);
			$days = $weeks * 7;
			
			$q = 'UPDATE pull_track SET instance = '.($weeks + 1).' WHERE id = 1';
			mysql_query($q);

$q = "SELECT DISTINCT(lead_id)
		 		FROM pull_history
		 		LEFT JOIN leads USING(lead_id)
		 		WHERE DATE(pulled_date) BETWEEN DATE_ADD('2016-01-08', INTERVAL ".$days." DAY) AND DATE_ADD('2016-01-08', INTERVAL ".$weeks." WEEK)
		 		ORDER BY leads.rep_id, pull_history.pull_id ASC";
		 		echo $q;
 	$res = mysql_query($q, $conn);
	
 	$currentRep = "";
 	$currentLeadCount = 0;
 	while($roww = mysql_fetch_assoc($res))
 	{
 		$leads = "SELECT *
				FROM leads
				LEFT JOIN reps ON reps.id = leads.rep_id
				WHERE lead_id = $roww[lead_id]
				ORDER BY date DESC";
		$result = mysql_query($leads, $conn);
 		
 		while($row2 = mysql_fetch_assoc($result)) {
 			if (strpos($row2['rep_id'], 'M') !== false) {
 				continue;
 			}

			// We've reached a new sales rep in the list - save lead count
			if($currentRep != $row2['name'])
			{
				if($currentRep != "")
				{
					$leadCount[] = array('name'=>$currentRep, 'count'=>$currentLeadCount);
					$docx->addBreak('page');
				}
				$currentRep = $row2['name'];
				$currentLeadCount = 0;
			}
			$currentLeadCount++;
			
			// Purchase history
			$q = "SELECT invoices.invoice_id, DATE_FORMAT(invoices.purchase_date, '%c/%d/%y') AS purchase_date, invoices.ship_date, invoices.paid_date, items.quantity, items.price, products.product_code
					FROM invoices
					INNER JOIN items USING(invoice_id) 
					INNER JOIN products USING(product_id)
					WHERE lead_id = " . $row2['lead_id'] . 
					" ORDER BY invoices.purchase_date DESC
					LIMIT 1";
			$r = mysql_query($q, $conn);
			
			if(mysql_num_rows($r))
			{
				$rr = mysql_fetch_assoc($r);

				
					if($rr['ship_date'] == '0000-00-00' or $rr['paid_date'] == '0000-00-00')
					{
						$currentLeadCount--;
						continue;
					}	
					
					if ($rr['invoice_id'] == '') {
						$currentLeadCount--; continue;
					}
			}
			$sales_rep = str_replace(' ', '_', trim($row2['name']));
			$docx->addText("Spsn: _" . $row2['rep_id'] . "__ " . $row2['name'] . "_______________" . date('m/d/Y') . "___Property of G3 Graphics__", $paramsText);
				
			$t = stripslashes(htmlspecialchars(convert_smart_quotes($row2['business'])));
			$docx->addText($t, $paramsText);
				
			$t = stripslashes(htmlspecialchars(convert_smart_quotes($row2['address'], ENT_QUOTES)));
			$docx->addText($t . "\t\tPr " . $row2['phone'], $paramsText);
				
			$docx->addText(htmlspecialchars(convert_smart_quotes($row2['city'])) . "\t" . $row2['state'] . " " . $row2['zipcode'] . ((!empty($row2['fax'])) ? "\tAl " . $row2['fax'] : ""), $paramsText);
				
			$docx->addText(stripslashes(htmlspecialchars(convert_smart_quotes($row2['contact']))),  $paramsText);
				
			$q = "UPDATE leads SET `last_pulled`=CURDATE() WHERE lead_id = " . $row2['lead_id'];
			mysql_query($q, $conn);
			
			$q = "INSERT INTO pull_history (lead_id) VALUES('$row2[lead_id]')";
			mysql_query($q, $conn);
			//		


			$numLines = 5;
			if(mysql_num_rows($r))
			{
				//$docx->addText("Invoice   Item\tQty\tAmt", $paramsText);
				//$numLines++;
				$l = 2;
				while($row = mysql_fetch_assoc($r))
				{
					if($l < 3)
					{
						$l++;
						$row3 = mysql_fetch_assoc($r);
						
						$ad_copy1 = explode("\r\n", stripslashes(htmlspecialchars(convert_smart_quotes($row['ad_copy']))));
						$ad_copy2 = explode("\r\n", stripslashes(htmlspecialchars(convert_smart_quotes($row3['ad_copy']))));
						
						$lines = count($ad_copy1);
						if(count($ad_copy2) > count($ad_copy1))
							$lines = count($ad_copy2);
						
						$invoice1 = $row['invoice_id'] . "   " . $row['product_code'] . "\t" . $row['quantity'] . "\t" . $row['price'] . "\t " . $row['purchase_date'];
						$invoice2 = $row3['invoice_id'] . "   " . $row3['product_code'] . "\t" . $row3['quantity'] . "\t" . $row3['price'] . "\t " . $row3['purchase_date'];
						//$inv = str_pad($invoice1 . "  !" . $invoice2, $SPACES, " ", STR_PAD_BOTH);
						
						$docx->addText($invoice1 . " ! " . $invoice2, $paramsText);
						$numLines++;
						
						if($lines > 2)
						{
							for($k = 0; $k < $lines; $k++)
							{
								$ad1 = str_pad($ad_copy1[$k], $SPACES/2, " ", STR_PAD_BOTH);
								$ad2 = str_pad($ad_copy2[$k], $SPACES/2, " ", STR_PAD_BOTH);
								$t = $ad1 . (($ad1 == "") ? "\t\t" : "") . "\t! " . $ad2;
								$docx->addText($t, $paramsText);
								$numLines++;
							}
						}
					}
					else
					{
						$docx->addText($row['invoice_id'] . "   " . $row['product_code'] . "\t" . $row['quantity'] . "\t" . $row['price'] . "\t " . $row['purchase_date'], $paramsText);
						$numLines++;
					}
				}
			}
				
			if($currentLeadCount % 2 == 0)
				$docx->addBreak('page');
			else
			{
				// Start next lead in the middle of the page
				$linebreaks = ($PAGE_LINES / 2) - $numLines;
				for($l = 0; $l < $linebreaks; $l++)
					$docx->addBreak('line');
					$docx->addText(" - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ");
			}
		} 
 	}
	
	// Add very last sales rep
	$leadCount[] = array('name'=>$currentRep, 'count'=>$currentLeadCount);
	
	// Statistics
	$docx->addBreak('page');
	$docx->addTitle("Statistics", array('val'=>1, 'sz'=>16));
	$total = 0;
	for($i = 0; $i < count($leadCount); $i++)
	{
		if($leadCount[$i]['count'] > 0)
		{
			$docx->addText($leadCount[$i]['name'] . "\t" . $leadCount[$i]['count'], $paramsText);
			$total += $leadCount[$i]['count'];
		}
	}
	$docx->addText("TOTAL\t" . $total, $paramsText);
	
	$filename = 'myleads_fix';
	$docx->createDocx($filename);
	
	/*function convert_smart_quotes($string) 
	{
		$search = array(chr(145), 
						chr(146), 
						chr(147), 
						chr(148), 
						chr(151),
						chr(189),
						chr(38),
						chr(190),
						chr(188)); 

		$replace = array("'", 
						 "'", 
						 '"', 
						 '"', 
						 '-',
						 '1/2',
						 'and',
						 '3/4',
						 '1/4'); 

		return str_replace($search, $replace, $string); 
	}*/
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment; filename=' . $filename . '.docx');
	header('Content-Length: ' . filesize('./' . $filename . '.docx'));
	readfile('./' . $filename . '.docx'); 
?>