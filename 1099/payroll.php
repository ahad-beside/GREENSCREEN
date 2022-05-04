<?php
	session_start();
	error_reporting(E_ALL);
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('phpexcel/PHPExcel.php');
	include('phpexcel/PHPExcel/Writer/Excel2007.php');
	
	if($_SESSION['level'] != 1)
	{
		echo "<center><b>You don't have access to this</b></center>";
		return;
	}
	
	if(isset($_POST['submitButton']))
	{
		$startInvoice = mysql_real_escape_string($_POST['startInvoice']);
		$endInvoice = mysql_real_escape_string($_POST['endInvoice']);
		
		$q = "INSERT INTO payroll (start_invoice, end_invoice) VALUES('$startInvoice', '$endInvoice')";
		mysql_query($q, $conn);
		
		$q = "SELECT * FROM reps
				WHERE rep_id NOT REGEXP '^M'
				AND rep_id != '02'
				AND rep_id != '08'
				AND rep_id != '12'
				AND rep_id != '95'
				AND rep_id != '99'";
		$result = mysql_query($q, $conn);
		
		$excel = new PHPExcel();
		$worksheet = 0;
		$totals = array();
		
		while($rep = mysql_fetch_assoc($result))
		{
			$excel->setActiveSheetIndex($worksheet++);
			setupDoc($excel);
			
			$currentRow = 5;
			
			// Sale's rep profile
			$repName = $rep['rep_id'] . ' ' . $rep['name'];
			$reload = $rep['reload_comm'];
			$front = $rep['front_comm'];
			$bonusLvl = $rep['bonus_level'];
			$bonusPercentage = $rep['bonus_percentage'];
			$belowParAllowed = $rep['below_par'];
			
			$totalClosed = 0;
			$totalOpened = 0;
			$totalComm = 0;
			$bonus = 0;
			$chargebacks = 0;
			$invoiceCount = 0;
			
			// Sale's rep
			$excel->getActiveSheet()->SetCellValue('A3', $repName);
			$excel->getActiveSheet()->setTitle($rep['name']);
			
			// Date
			$excel->getActiveSheet()->SetCellValue('G3', date('m/d/Y'));
			
			$q = "SELECT invoices.*, items.product_id, items.quantity, items.price, leads.business, leads.state, products.product_code
					FROM products, invoices
					INNER JOIN items USING(invoice_id)
					INNER JOIN leads USING(lead_id)
					WHERE (invoices.rep_id = $rep[id] OR invoices.dialer_id = $rep[id])
					AND products.product_id = items.product_id
					AND invoice_id >= $startInvoice
					AND invoice_id <= $endInvoice
					ORDER BY invoice_id ASC";
			$r = mysql_query($q, $conn);
			
			while($row = mysql_fetch_assoc($r))
			{
				$soldPrice = $row['price'];
				$comm = 0;
				$extendedPrice = 0;
				$dialerRep = 0;
				$invoiceCount++; 
				$soldBelowPar = false;
				
				// Check if is dialer
				$isDialer = false;
				if($row['dialer_id'] == $rep['id'])
					$isDialer = true;
						
				if($soldPrice > 0)
				{
					// What is base price(extended_price) for quantity sold
					$q = "SELECT * FROM prices WHERE product_id = " . $row['product_id'] . " ORDER BY quantity ASC";
					$rr = mysql_query($q, $conn);
					while($row2 = mysql_fetch_assoc($rr))
					{
						if($row2['quantity'] <= $row['quantity'])
							$extendedPrice = $row2['extended_price'];
						else
							break;
					}
					
					// Check if its a front or reload
					$isReload = true;
					$q = "SELECT COUNT(*) as count FROM invoices WHERE lead_id = " . $row['lead_id'];
					$rr = mysql_fetch_assoc(mysql_query($q, $conn));
					if($rr['count'] == 1)
						$isReload = false;
					
					// Check that the item was sold at lowest allowed price
					$lowestAllowed = $extendedPrice - ($extendedPrice * ($belowParAllowed / 100));
					if($soldPrice >= $lowestAllowed)
					{
						// Reload commission
						if($isReload)
							$comm = $extendedPrice * ($reload / 100);
						// Front commission
						else 
							$comm = $extendedPrice * ($front / 100);
						
						$q = "UPDATE invoices SET commission = '$comm' WHERE invoice_id = $row[invoice_id]";
						mysql_query($q, $conn);
						
						// Sold item for more than base price -- calculate bonus
						if($soldPrice > $extendedPrice)
						{
							$diff = ($soldPrice - $extendedPrice) / 2;
							$comm += $diff;
							
							$q = "UPDATE invoices SET bonus = '$diff' WHERE invoice_id = $row[invoice_id]";
							mysql_query($q, $conn);
						}
						
						// Split commission -- half the commision
						if($row['dialer_id'] != 0)
						{
							$comm *= 0.5;
							
							if(!$isDialer)
								$q = "SELECT rep_id FROM reps WHERE id = " . $row['dialer_id'];
							else
								$q = "SELECT rep_id FROM reps WHERE id = " . $row['rep_id'];
							
							$rr = mysql_query($q, $conn);
							$rr = mysql_fetch_assoc($rr);
							$dialerRep = $rr['rep_id'];
						}
					}
					else
						$soldBelowPar = true;
					
					if(!$isDialer)
						$totalClosed += $soldPrice;
					else
						$totalOpened += $soldPrice;
					
					$totalComm += $comm;
				}
				
				$excel->getActiveSheet()->SetCellValue('A' . $currentRow, $row['invoice_id']);
				$excel->getActiveSheet()->SetCellValue('B' . $currentRow, stripslashes(convert_smart_quotes($row['business'])));
				$excel->getActiveSheet()->SetCellValue('C' . $currentRow, $row['quantity']);
				$excel->getActiveSheet()->SetCellValue('D' . $currentRow, $row['product_code']);
				
				if(!$isDialer)
					$excel->getActiveSheet()->SetCellValue('E' . $currentRow, $row['price']);
				else
					$excel->getActiveSheet()->SetCellValue('F' . $currentRow, $row['price']);
				
				$excel->getActiveSheet()->SetCellValue('G' . $currentRow, $comm);
				
				if($row['dialer_id'] != 0)
					$excel->getActiveSheet()->SetCellValue('H' . $currentRow, '**' . $dialerRep);
				if($soldBelowPar)
					$excel->getActiveSheet()->SetCellValue('H' . $currentRow, '*');
				
				// Increment row
				$currentRow++;
				
				// Add extra line for expense
				if($row['expense_description'] != '' and $row['expense_amount'] != '')
				{
					$excel->getActiveSheet()->setCellValue('B' . $currentRow, stripslashes(convert_smart_quotes($row['business'])));
					$excel->getActiveSheet()->setCellValue('D' . $currentRow, $row['expense_description']);
					$excel->getActiveSheet()->setCellValue('G' . $currentRow, '-' . $row['expense_amount']);
					
					$currentRow++;
				}
			}
			
			// Bonus
			if($bonusLvl > 0 && $totalClosed > $bonusLvl)
				$bonus = ($totalClosed - $bonusLvl) * ($bonusPercentage / 100);
			
			// TODO: check for any chargebacks
			
			$tmp = array($rep['name'], $totalClosed, $invoiceCount);
			$totals[] = $tmp;
			
			// Statement summary
			$excel->getActiveSheet()->SetCellValue('B36', 'TOTAL ' . $invoiceCount);
			
			$excel->getActiveSheet()->SetCellValue('E36', '=SUM(E5:E35)');
			$excel->getActiveSheet()->SetCellValue('F36', '=SUM(F5:F35)');
			$excel->getActiveSheet()->SetCellValue('G36', '=SUM(G5:G35)');
			
			$excel->getActiveSheet()->SetCellValue('C38', 'BONUS');
			$excel->getActiveSheet()->SetCellValue('G38', $bonus);
			
			$excel->getActiveSheet()->SetCellValue('C39', 'VACATION');
			$excel->getActiveSheet()->SetCellValue('G39', '0.00');
			
			$excel->getActiveSheet()->SetCellValue('C40', 'BONUS');
			$excel->getActiveSheet()->SetCellValue('G40', '0.00');
			
			$excel->getActiveSheet()->SetCellValue('C41', 'CHARGEBACKS');
			$excel->getActiveSheet()->SetCellValue('G41', $chargebacks);
			
			$excel->getActiveSheet()->SetCellValue('C42', 'GROSS');
			$excel->getActiveSheet()->SetCellValue('G42', '=SUM(G36,G38,-G41)');
			
			$excel->getActiveSheet()->SetCellValue('C44', 'TOTAL VOLUME');
			$excel->getActiveSheet()->SetCellValue('G44', '=SUM(E36:F36)');
			
			// Create new worksheet
			$excel->createSheet();
		}
		
		// Payroll summary
		$excel->setActiveSheetIndex($worksheet);
		$excel->getActiveSheet()->setTitle("Summary");
		setupSummary($excel);
		$currentRow = 5;
		
		for($i = 0; $i < count($totals); $i++)
		{
			$excel->getActiveSheet()->SetCellValue('A' . $currentRow, $totals[$i][0]);
			$excel->getActiveSheet()->SetCellValue('B' . $currentRow, $totals[$i][1]);
			$excel->getActiveSheet()->SetCelLValue('C' . $currentRow, $totals[$i][2]);
			$excel->getActiveSheet()->SetCellValue('D' . $currentRow, "=B$currentRow/C$currentRow");
			
			$currentRow++;
		}
		
		$totalRow = $currentRow + 3;
		$excel->getActiveSheet()->SetCellValue('A' . $totalRow, 'TOTAL');
		$excel->getActiveSheet()->SetCellValue('B' . $totalRow, '=SUM(B5:B' . $currentRow . ')');
		$excel->getActiveSheet()->SetCellValue('C' . $totalRow, '=SUM(C5:C' . $currentRow . ')');
		$excel->getActiveSheet()->SetCellValue('D' . $totalRow, "=SUM(D5:D$currentRow) / " . count($totals));
		
		// Summary border
		$border = array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array(
				'rgb' => '000000'
			)
		);
		$excel->getActiveSheet()->getStyle('A' . $totalRow . ':D' . $totalRow)->getBorders()->getTop()->applyFromArray($border);
		
		// Create document
		$writer = new PHPExcel_Writer_Excel2007($excel);
		$writer->save('payroll.xlsx');
		
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename=payroll.xlsx');
		header('Content-Length: ' . filesize('./payroll.xlsx'));
		readfile('./payroll.xlsx');
	}
	
	function setupDoc($excel)
	{
		// Set column widths
		$excel->getActiveSheet()->getColumnDimension('A')->setWidth(9.29);
		$excel->getActiveSheet()->getColumnDimension('B')->setWidth(34.57);
		$excel->getActiveSheet()->getColumnDimension('C')->setWidth(6.71);
		$excel->getActiveSheet()->getColumnDimension('D')->setWidth(7.71);
		$excel->getActiveSheet()->getColumnDimension('E')->setWidth(8.57);
		$excel->getActiveSheet()->getColumnDimension('F')->setWidth(8.57);
		$excel->getActiveSheet()->getColumnDimension('G')->setWidth(8.43);
		$excel->getActiveSheet()->getColumnDimension('H')->setWidth(4.14);
		
		// Set row heights
		$excel->getActiveSheet()->getRowDimension('1')->setRowHeight(21);
		$excel->getActiveSheet()->getRowDimension('2')->setRowHeight(21);
		$excel->getActiveSheet()->getRowDimension('3')->setRowHeight(21);
		$excel->getActiveSheet()->getRowDimension('4')->setRowHeight(33);
		
		// Column headers
		$excel->getActiveSheet()->SetCellValue('A1', 'G3 GRAPHICS, INC.');
		$excel->getActiveSheet()->SetCellValue('A2', 'COMMISION STATEMENT');
		$excel->getActiveSheet()->SetCellValue('A4', 'INVOICE');
		$excel->getActiveSheet()->SetCellValue('B4', 'NAME');
		$excel->getActiveSheet()->SetCellValue('C4', 'QTY');
		$excel->getActiveSheet()->SetCellValue('D4', 'ITEM');
		$excel->getActiveSheet()->SetCellValue('E4', 'CLOSED VOLUME');
		$excel->getActiveSheet()->SetCellValue('F4', 'OPENED VOLUME');
		$excel->getActiveSheet()->SetCellValue('G4', 'COMM.');
		
		$excel->getActiveSheet()->SetCellValue('B41', '* UNDER PAR');
		$excel->getActiveSheet()->SetCellValue('B42', '** SHARED COMMISSION');
		
		// Merge cells
		$excel->getActiveSheet()->mergeCells('A1:H1');
		$excel->getActiveSheet()->mergeCells('A2:H2');
		$excel->getActiveSheet()->mergeCells('A3:B3');
		$excel->getActiveSheet()->mergeCells('C38:D38');
		$excel->getActiveSheet()->mergeCells('C39:D39');
		$excel->getActiveSheet()->mergeCells('C40:D40');
		$excel->getActiveSheet()->mergeCells('C41:D41');
		$excel->getActiveSheet()->mergeCells('C42:D42');
		$excel->getActiveSheet()->mergeCells('C44:D44');
		$excel->getActiveSheet()->mergeCells('G3:H3');
		
		// Font
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('G44')->getFont()->setBold(true);
		
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(16);
		
		// Text style
		$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$excel->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$excel->getActiveSheet()->getStyle('F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$excel->getActiveSheet()->getStyle('E4')->getAlignment()->setWrapText(true);
		$excel->getActiveSheet()->getStyle('F4')->getAlignment()->setWrapText(true);
		
		// Left justify
		$excel->getActiveSheet()->getStyle('A5:A35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$excel->getActiveSheet()->getStyle('C5:C35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		
		// Number format
		$excel->getActiveSheet()->getStyle('E5:E35')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('F5:F35')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G5:G35')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		$excel->getActiveSheet()->getStyle('E36')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('F36')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G36')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G38')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G39')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G40')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G41')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G42')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('G44')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		// Right justify
		$excel->getActiveSheet()->getStyle('C38')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$excel->getActiveSheet()->getStyle('C39')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$excel->getActiveSheet()->getStyle('C40')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$excel->getActiveSheet()->getStyle('C41')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$excel->getActiveSheet()->getStyle('C42')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$excel->getActiveSheet()->getStyle('C44')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$excel->getActiveSheet()->getStyle('D5:D35')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		// Summary border
		$border = array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array(
				'rgb' => '000000'
			)
		);
		$excel->getActiveSheet()->getStyle('A35:G35')->getBorders()->getBottom()->applyFromArray($border);
		$excel->getActiveSheet()->getStyle('G42')->getBorders()->getTop()->applyFromArray($border);
	}
	
	function setupSummary($excel)
	{
		// Set column widths
		$excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
		$excel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
		$excel->getActiveSheet()->getColumnDimension('C')->setWidth(8.43);
		$excel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
		
		// Merge cells
		$excel->getActiveSheet()->mergeCells('A1:E1');
		$excel->getActiveSheet()->mergeCells('A2:E2');
		$excel->getActiveSheet()->mergeCells('A3:E3');
		
		// Font
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
		$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
		$excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(16);
		$excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(16);
		
		// Text style
		$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		// Number format
		$excel->getActiveSheet()->getStyle('B5:B35')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$excel->getActiveSheet()->getStyle('D5:D35')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		
		$excel->getActiveSheet()->SetCellValue('A1', 'G3 GRAPHICS, INC.');
		$excel->getActiveSheet()->SetCellValue('A2', 'WEEKLY VERIFICATION');
		$excel->getActiveSheet()->SetCellValue('A3', 'AVERAGE');
		$excel->getActiveSheet()->SetCellValue('E4', date('m/d/Y'));
	}
	
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
	
	include('header.php');
?>
		<form name="payrollForm" method="post" action="">
			<table>
				<tr>
					<td>
						Starting Invoice:&nbsp;&nbsp;
						<input type="text" name="startInvoice" size="7" />
					</td>
					<td>
						Ending Invoice:&nbsp;&nbsp;
						<input type="text" name="endInvoice" size="7" />
					</td>
					<td><input type="submit" name="submitButton" /></td>
				</tr>
			</table>
		</form>
	</div>
</html>