<?php 

	include('config.php');
	require_once('./phpdocx/classes/CreateDocx.inc');
	require_once('./PhpWord/Autoloader.php');
	\PhpOffice\PhpWord\Autoloader::register();

	// $qr = "SET @@session.optimizer_search_depth=0"; mysql_query($qr, $conn);
	session_start();
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	$PAGE_LINES = 26;
	$SPACES = 120;
	$paramsText = array(
		'lineSpacing' => 480,
		'sz' => 9
	);
	
	if(isset($_POST['submitButton']))
	{
		$rep_id = array();
		if($_POST['rep_id'] == -1)
		{
			//$q = 'SELECT instance FROM pull_track WHERE id = 1';
			//$weeks = mysql_result(mysql_query($q), 0);
			//$days = $weeks * 7;

			// All Lead Fix
			//$q = "SELECT DISTINCT(lead_id)
				//		FROM pull_history
				//		LEFT JOIN leads USING(lead_id)
				//		WHERE DATE(pulled_date) BETWEEN DATE_ADD('2013-07-05', INTERVAL ".$days." DAY) AND DATE_ADD('2013-07-05', INTERVAL ".$weeks." WEEK)
			//			ORDER BY leads.rep_id, pull_history.pull_id ASC";
		 		
		 	// $res = mysql_query($q, $conn);
		 	// if(mysql_num_rows($res))
				//include('./pullfix.php');

			$q = "SELECT * FROM reps";
			$r = mysql_query($q, $conn);

			 if(mysql_num_rows($r) > 0)
			 {
			 	while($row = mysql_fetch_assoc($r))
			 	{
			 		if(preg_match('/^M[0-9]+/i', $row['rep_id']) == 0)
			 		$rep_id[] = $row['id'];
			 	}
			 }
		}
		else
			$rep_id[] = mysql_real_escape_string($_POST['rep_id']);
		
		$leadCount = array();
		$docx = new CreateDocx();
		for($index = 0; $index < count($rep_id); $index++)
		{
		// MOD DATE DATE_SUB(NOW(), INTERVAL 35 DAY)
			$leads = "SELECT *
			 			FROM leads
			 			LEFT JOIN reps ON reps.id = leads.rep_id
			 			WHERE reps.id = " . $rep_id[$index] . 
			   			" ORDER BY date DESC";
			    $result = mysql_query($leads, $conn);

			// if($week == 1) {
				// $count = mysql_num_rows($result) / $maxWeeks;
				$count = mysql_num_rows($result);
			// 	$qq = "UPDATE reps SET weekly_leads = " . $count . " WHERE id = " . $rep_id[$index];
			// 	mysql_query($qq, $conn);
			// } else {
			// 	$qq = "SELECT weekly_leads FROM reps WHERE id = " . $rep_id[$index];
			// 	$rr = mysql_query($qq, $conn);
			// 	$rrr = mysql_fetch_assoc($rr);
			// 	$count = $rrr['weekly_leads'];
			// }
			
			$i = 0;
			$sales_rep = "";
			while($row2 = mysql_fetch_assoc($result)) {
				if($i++ < $count) {
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

							if($rr['paid_date'] == '0000-00-00')
							{
								$i--;
								continue;
							}	
						
					}
					
					// Reset last_pulled so as to not pull this again for at least 30days
					// MOD DATE
					$q = "UPDATE leads SET `last_pulled`='2002-02-04' WHERE lead_id = " . $row2['lead_id'];
					mysql_query($q, $conn);
					
					$q = "INSERT INTO pull_history (lead_id) VALUES('$row2[lead_id]')";
					mysql_query($q, $conn);
					
					$sales_rep = str_replace(' ', '_', trim($row2['name']));
					$docx->addText("Spsn: _" . $row2['rep_id'] . "__ " . $row2['name'] . "_______________" . date('m/d/Y') . "___Property of G3 Graphics__", $paramsText);
					
					$t = stripslashes(htmlspecialchars(convert_smart_quotes($row2['business'])));
					$docx->addText($t, $paramsText);
					
					$t = stripslashes(htmlspecialchars(convert_smart_quotes($row2['address'], ENT_QUOTES)));
					$docx->addText($t . "\t\tPr " . $row2['phone'], $paramsText);
					
					$docx->addText(htmlspecialchars(convert_smart_quotes($row2['city'])) . "\t" . $row2['state'] . " " . $row2['zipcode'] . ((!empty($row2['fax'])) ? "\tAl " . $row2['fax'] : ""), $paramsText);
					
					$docx->addText(stripslashes(htmlspecialchars(convert_smart_quotes($row2['contact']))),  $paramsText);
					
					$q = "SELECT invoices.invoice_id, DATE_FORMAT(invoices.purchase_date, '%c/%d/%y') AS purchase_date, items.quantity, items.price, products.product_code
							FROM invoices
							INNER JOIN items USING(invoice_id) 
							INNER JOIN products USING(product_id)
							WHERE lead_id = " . $row2['lead_id'] . 
							" ORDER BY invoices.purchase_date DESC
							LIMIT 10";
					$r = mysql_query($q, $conn);
					
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
					
					if($i % 2 == 0) {
						$docx->addBreak('page');
					} else {
						// Start next lead in the middle of the page
						$linebreaks = ($PAGE_LINES / 2) - $numLines;
						for($l = 0; $l < $linebreaks; $l++)
							$docx->addBreak('line');
						$docx->addText(" - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ");
					}
				}
				else
					break;
			}
			$leadCount[] = $i;
			
			if($index+1 < count($rep_id))
				$docx->addBreak('page');
		}
		
		// Statistics
		$docx->addBreak('page');
		$docx->addTitle("Statistics Week " . $week, array('val'=>1, 'sz'=>16));
		$total = 0;
		for($i = 0; $i < count($rep_id); $i++)
		{
			$q = "SELECT * FROM reps WHERE id = " . $rep_id[$i];
			$r = mysql_query($q, $conn);
			$row = mysql_fetch_assoc($r);
			
			if($leadCount[$i] > 0)
			{
				$docx->addText($row['name'] . "\t" . $leadCount[$i], $paramsText);
				$total += $leadCount[$i];
			}
		}
		$docx->addText("TOTAL\t" . $total, $paramsText);
		
		// Increment week count in config.php
		$lines = file('config.php');
		for($m = 0; $m < count($lines); $m++)
		{
			if(strstr($lines[$m], '$week'))
			{
				if($week == $maxWeeks)
					$lines[$m] = "\t" . '$week = 1;' . "\r\n";
				else
					$lines[$m] = "\t" . '$week = ' . ++$week . ";\r\n";
			}
		}
		
		$fp = fopen('config.php', 'w+');
		foreach($lines as $line)
			fwrite($fp, $line);
		fclose($fp);
		
		$docx->createDocx('myleads');
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment; filename=myleads.docx');
		header('Content-Length: ' . filesize('./myleads.docx'));
		readfile('./myleads.docx');
	}
	else if(isset($_POST['hotButton']))
	{
		$date = mysql_real_escape_string($_POST['date']);
		if(empty($date))
			$date = date('Y-m-d');
		
		/*$q = "SELECT invoices.invoice_id, DATE_FORMAT(invoices.purchase_date, '%c/%d/%y') AS purchase_date, invoices.paid_date, items.quantity, items.price, products.product_code,
				reps.rep_id AS repid, reps.name,
				leads.*
				FROM invoices
				INNER JOIN items USING(invoice_id) 
				INNER JOIN products USING(product_id)
				INNER JOIN reps ON reps.id = invoices.rep_id
				INNER JOIN leads USING(lead_id)
				WHERE paid_date = '$date'
				ORDER BY repid ASC";*/
				//WHERE paid_date < DATE_SUB('$date', INTERVAL 7 DAY)
		$q = "
			SELECT leads.*, reps.rep_id AS repid, reps.name 
			FROM leads 
			INNER JOIN reps ON reps.id = leads.rep_id 
			WHERE lead_id IN (SELECT DISTINCT lead_id FROM invoices WHERE paid_date = '$date') 
			ORDER BY repid ASC"; 
		$r = mysql_query($q, $conn);
		$rowCount = mysql_num_rows($r); // ERR
		
		$i = 0;
		$reps = array();
		$leadCount = array();
		$count = 0;
		// $docx = new CreateDocx();

		$docxn = new \PhpOffice\PhpWord\PhpWord();
		$csec = $docxn->addSection();

		$currRep = "";		// Keep track of which sales rep were are currently on --> cannot have two leads with diff reps on same page
		$newpage = true;
		$runningRowCount = 1;

		while($row = mysql_fetch_assoc($r)) // ERR
		{
			if($i == 0)
			{
				$currRep = $row['repid'];
				$reps[] = $row['repid'];
				$count++;
			}
			else if($currRep != $row['repid'])
			{
				$currRep = $row['repid'];
				$reps[] = $row['repid'];
				$leadCount[] = $count;
				$count = 1;
				
				if(!$newpage)
				{
					// $docx->addBreak('page');
					$csec->addPageBreak();
					$i--;
				}
			}
			else
				$count++;

			// Record last lead's count
			if($runningRowCount++ == $rowCount)
			{
				$leadCount[] = $count;
			}
			
			// Reset last_pulled so as to not pull this again for at least 30days
			$q = "UPDATE leads SET `last_pulled`=CURDATE() WHERE lead_id = " . $row['lead_id'];
			mysql_query($q, $conn);
			
			$sales_rep = str_replace(' ', '_', trim($row['name']));
			// $docx->addText("Spsn: _" . $row['repid'] . "__ " . $row['name'] . "_______________" . date('m/d/Y') . "___Property of G3 Graphics__", $paramsText);

			$csec->addText(
				htmlspecialchars(
"Spsn: _" . $row['repid'] . "__ " . $row['name'] . "_______________" . date('m/d/Y') . "___Property of G3 Graphics__"
				)
			);
			
			$t = stripslashes(htmlspecialchars(convert_smart_quotes($row['business'])));
			// $docx->addText(htmlspecialchars(convert_smart_quotes($row['business'])), $paramsText);
			
			$csec->addText(
				htmlspecialchars(convert_smart_quotes($row['business']))
			);

			$t = stripslashes(htmlspecialchars(convert_smart_quotes($row['address'], ENT_QUOTES)));
			// $docx->addText($t . "\t\tPr " . $row['phone'], $paramsText);
			// $docx->addText(htmlspecialchars(convert_smart_quotes($row['city'])) . "\t" . $row['state'] . " " . $row['zipcode'] . ((!empty($row['fax'])) ? "\tAl " . $row['fax'] : ""), $paramsText);			
			// $docx->addText(stripslashes(htmlspecialchars(convert_smart_quotes($row['contact']))),  $paramsText);

			$csec->addText(
				htmlspecialchars($t . "\t\tPr " . $row['phone'])
			);

			$csec->addText(
				htmlspecialchars(htmlspecialchars(convert_smart_quotes($row['city'])) . "\t" . $row['state'] . " " . $row['zipcode'] . ((!empty($row['fax'])) ? "\tAl " . $row['fax'] : ""))
			);

			$csec->addText(
				htmlspecialchars(stripslashes(convert_smart_quotes($row['contact'])))
			);
			
			$q = "SELECT invoices.invoice_id, DATE_FORMAT(invoices.purchase_date, '%c/%d/%y') AS purchase_date, items.ad_copy, items.quantity, items.price, products.product_code
					FROM invoices
					INNER JOIN items USING(invoice_id) 
					INNER JOIN products USING(product_id)
					WHERE lead_id = " . $row['lead_id'] . 
					" ORDER BY invoices.purchase_date DESC
					LIMIT 10";
			$rr = mysql_query($q, $conn);
			
			$numLines = 5;
			if(mysql_num_rows($rr))
			{
				//$docx->addText("Invoice   Item\tQty\tAmt", $paramsText);
				//$numLines++;
				$l = 2;
				while($row2 = mysql_fetch_assoc($rr))
				{
					if($l < 3)
					{
						$l++;
						$row3 = mysql_fetch_assoc($rr);
						
						$ad_copy1 = explode("\r\n", stripslashes(htmlspecialchars(convert_smart_quotes($row2['ad_copy']))));
						$ad_copy2 = explode("\r\n", stripslashes(htmlspecialchars(convert_smart_quotes($row3['ad_copy']))));
						
						$lines = count($ad_copy1);
						if(count($ad_copy2) > count($ad_copy1))
							$lines = count($ad_copy2);
						
						$invoice1 = $row2['invoice_id'] . "   " . $row2['product_code'] . "\t" . $row2['quantity'] . "\t" . $row2['price'] . "\t " . $row2['purchase_date'];
						$invoice2 = $row3['invoice_id'] . "   " . $row3['product_code'] . "\t" . $row3['quantity'] . "\t" . $row3['price'] . "\t " . $row3['purchase_date'];
						//$inv = str_pad($invoice1 . "  !" . $invoice2, $SPACES, " ", STR_PAD_BOTH);
						
						// $docx->addText($invoice1 . " ! " . $invoice2, $paramsText);

						$csec->addText(
							htmlspecialchars($invoice1 . " ! " . $invoice2)
						);

						$numLines++;
						
						for($k = 0; $k < $lines; $k++)
						{
							$ad1 = str_pad($ad_copy1[$k], $SPACES/2, " ", STR_PAD_BOTH);
							$ad2 = str_pad($ad_copy2[$k], $SPACES/2, " ", STR_PAD_BOTH);
							$t = $ad1 . (($ad1 == "") ? "\t\t" : "") . "\t! " . $ad2;
							// $docx->addText($t, $paramsText);

							$csec->addText(htmlspecialchars($t));
							
							$numLines++;
						}
					}
					else if($l == 3)
					{
						$inv = $row2['invoice_id'] . "   " . $row2['product_code'] . "\t" . $row2['quantity'] . "\t". $row2['price'] . "\t" . $row2['purchase_date'];
						// $docx->addText($inv, $paramsText);

						$csec->addText($inv);
						// $csec->addPageBreak();
						$numLines++;
						
						// Check if the third invoice has an ad copy
						/*if(empty($row2['ad_copy']))
						{
							$docx->addText($row2['invoice_id'] . "   " . $row2['product_code'] . "\t" . $row2['quantity'] . "\t" . $row2['price'] . "\t " . $row2['purchase_date'], $paramsText);
							$numLines++;
							
							while($rrow = mysql_fetch_assoc($rr))
							{
								$docx->addText($rrow['invoice_id'] . "   " . $rrow['product_code'] . "\t" . $rrow['quantity'] . "\t" . $rrow['price'] . "\t " . $rrow['purchase_date'], $paramsText);
								$numLines++;
							}
						}
						else
						{
							// Fourth invoice
							$row4 = mysql_fetch_assoc($rr);
							
							$invoice3 = $row2['invoice_id'] . "   " . $row2['product_code'] . "\t" . $row2['quantity'] . "\t" . $row2['price'] . "\t " . $row2['purchase_date'];
							$invoice4 = $row4['invoice_id'] . "   " . $row4['product_code'] . "\t" . $row4['quantity'] . "\t" . $row4['price'] . "\t " . $row4['purchase_date'];
							$docx->addText($invoice3 . "\t! " . $invoice4, $paramsText);
							$numLines++;
							
							$ad_copy3 = explode("\r\n", htmlspecialchars(convert_smart_quotes($row2['ad_copy'])));
							
							$lines = 0;
							while($rrow = mysql_fetch_assoc($rr))
							{
								// No more ad copy but still have invoices to show
								if($lines > count($ad_copy3))
								{
									$docx->addText("! " . $rrow['invoice_id'] . "   " . $rrow['product_code'] . "\t" . $rrow['quantity'] . "\t" . $rrow['price'] . "\t " . $rrow['purchase_date'], $paramsText);
									$numLines++;
								}
								else
								{
									$ad1 = str_pad($ad_copy3[$lines++], $SPACES/2, " ", STR_PAD_BOTH);
									$docx->addText($ad1 . "\t! " . $rrow['invoice_id'] . "   " . $rrow['product_code'] . "\t" . $rrow['quantity'] . "\t" . $rrow['price'] . "\t " . $rrow['purchase_date'], $paramsText);
									$numLines++;
								}
							}
							
							for($k = $lines; $k < count($ad_copy3); $k++)
							{
								$ad1 = str_pad($ad_copy3[$k], $SPACES/2, " ", STR_PAD_BOTH);
								$docx->addText($ad1 . "\t! ", $paramsText);
								$numLines++;
							}
						}*/
					}
					//$docx->addText($row2['invoice_id'] . "   " . $row2['product_code'] . "\t" . $row2['quantity'] . "\t" . $row2['price'] . "\t " . $row2['purchase_date'], $paramsText);
				}
			}
			
			if(++$i % 2 == 0)
			{
				// $docx->addBreak('page');
				$csec->addPageBreak();
				$newpage = true;
			}
			else
			{
				// Start next lead in the middle of the page
				$linebreaks = ($PAGE_LINES / 2) - $numLines;
				for($l = 0; $l < $linebreaks; $l++)
					$csec->addTextBread(1);
				// $docx->addText(" - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ");

				$csec->addText(htmlspecialchars(" - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - "));
				$newpage = false;
			}
		}
		// print_r($reps);echo"<br>";print_r($leadCount);
		
		// Statistics
		// $docx->addBreak('page');
		// $docx->addTitle("Statistics Hot Leads", array('val'=>1, 'sz'=>16));

		$csec->addPageBreak();
		$csec->addText("Statistics Hot Leads", array('size'=>16, 'color'=>'C0F7F7'));

		$total = 0;
		for($i = 0; $i < count($reps); $i++)
		{
			$q = "SELECT * FROM reps WHERE rep_id = '" . $reps[$i] . "'";
			$r = mysql_query($q, $conn);
			$row = mysql_fetch_assoc($r);
			
			// $docx->addText($row['name'] . "\t" . $leadCount[$i], $paramsText);

			$csec->addText(
				htmlspecialchars(
					$row['name'] . "\t" . $leadCount[$i]
				)
			);

			$total += $leadCount[$i];
		}
		// $docx->addText("TOTAL\t" . $total, $paramsText);
		// $docx->createDocx('hotleads');

		$csec->addText("TOTAL            ".$total);
		
		$objwriter = \PhpOffice\PhpWord\IOFactory::createWriter($docxn, 'Word2007');
		$objwriter->save('hotleadsnew.docx');
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment; filename=hotleads.docx');
		header('Content-Length: ' . filesize('./hotleadsnew.docx'));
		readfile('./hotleadsnew.docx');
	}
	else if(isset($_POST['unpaidButton'])) 
	{
		include('./reports.php');
	}
	else if(isset($_POST['notshippedButton']))
	{
		include('./reports_nonshipped.php');
	}
	else if(isset($_POST['repullButton']))
	{
		$pull_date = mysql_real_escape_string($_POST['pull_date']);
		$q = "SELECT DISTINCT(lead_id)
	 		FROM pull_history
	 		LEFT JOIN leads USING(lead_id)
	 		WHERE DATE(pulled_date) = '$pull_date'
	 		ORDER BY leads.rep_id, pull_history.pull_id ASC";
	 		
	 	$res = mysql_query($q, $conn);
	 	if(mysql_num_rows($res))
			include('./leads_repull.php');
	}
	
	function convert_smart_quotes($string) 
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
	}
	
	include('header.php');
?>
		<div style="color:#fff;margin:auto;width: 50%;text-align:center;">
			<h3>Leads</h3>
			<form name="leads" method="post" action="">
				Sales Rep:&nbsp;&nbsp;
				<select name="rep_id" style="width: 150px;"><?php print $q; ?>
					<option value=""></option>
					<option value="-1">All</option>
					<?php
						$q = "SELECT * FROM reps";
						$r = mysql_query($q, $conn);
						
						while($row = mysql_fetch_assoc($r)) { //ERR
					?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
					<?php
						}
					?>
				</select>&nbsp;&nbsp;
				<input type="submit" name="submitButton" value="Enter" style="width: 100px;" />
			</form>
			
			<hr style="width:40%;" />
			<form name="repull_leads" method="post" action="">
				Pull Date:&nbsp;
				<input type="text" name="pull_date" value="<?php echo date('Y-m-d'); ?>" size="12" />&nbsp;&nbsp;
				<input type="submit" name="repullButton" value="Re-pull Leads" />
				<?php
				if(isset($_POST['repullButton']))
				{
					$date = mysql_real_escape_string($_POST['date']);
					$q = "SELECT DISTINCT(lead_id)
				 		FROM pull_history
				 		LEFT JOIN leads USING(lead_id)
				 		WHERE DATE(pulled_date) = '$date'
				 		ORDER BY leads.rep_id, pull_history.pull_id ASC";
				 		
				 	$res = mysql_query($q, $conn);
				 	if(!mysql_num_rows($res))
				 		echo '<span style="color:#f00;">Invalid pull date</span>';
				}
			?>
			</form>
			
			<br />
			<h3>Hot Leads</h3>
			<form name="hot_leads" method="post" action="">
				<input type="text" name="date" value="<?php echo date('Y-m-d'); ?>" size="12" />&nbsp;&nbsp;
				<input type="submit" name="hotButton" value="Hot Leads" />
			</form>
			<br /><br />
			<form name="unpaid_form" method="post" action="">
				<h3>Unpaid Invoices</h3>
				<input type="submit" name="unpaidButton" value="Get Unpaid Invoices" />
			</form>
			<form name="notshipped_form" method="post" action="">
				<h3>No Shipping Date Invoices</h3>
				<input type="submit" name="notshippedButton" value="Get No-Shipping Invoices" />
			</form>
		</div>
	</body>
</html>