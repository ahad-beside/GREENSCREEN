<?php
	include('config.php');
	include('phpexcel/PHPExcel.php');
	include('phpexcel/PHPExcel/Writer/Excel2007.php');

	$excel = new PHPExcel();
	$worksheet = 0;
	
	// Non-paid invoices
	$excel->setActiveSheetIndex($worksheet++);
	$excel->getActiveSheet()->setTitle("Non-paid");
	$excel->getActiveSheet()->SetCellValue('A1', 'Purchase Date');
	$excel->getActiveSheet()->SetCellValue('B1', 'Invoice');
	$excel->getActiveSheet()->SetCellValue('C1', 'Sales Rep');
	$excel->getActiveSheet()->SetCellValue('D1', 'Business');
	$excel->getActiveSheet()->SetCellValue('E1', 'Product');
	$excel->getActiveSheet()->SetCellValue('F1', 'Count');
	$excel->getActiveSheet()->SetCellValue('G1', 'Price');
	
	$excel->getActiveSheet()->getColumnDimension('D')->setWidth(34.71);
	
	$q = "SELECT 
		invoices.invoice_id, invoices.lead_id, invoices.rep_id, invoices.dialer_id, invoices.purchase_date, invoices.purchase_date,
		leads.contact, leads.business, leads.address, leads.city, leads.state, leads.zipcode, leads.phone,
		reps.name, reps.rep_id AS rep,
		items.*,
		products.*
		FROM invoices 
		LEFT JOIN leads USING(lead_id) 
		LEFT JOIN reps ON reps.id = invoices.rep_id
		INNER JOIN items USING(invoice_id)
		INNER JOIN products USING(product_id)
		WHERE purchase_date <= DATE_SUB(NOW(), INTERVAL 30 DAY) 
			AND paid_date = '0000-00-00'
			AND reps.rep_id != '99'
			AND reps.rep_id != 'M2'
                ORDER BY reps.rep_id, purchase_date ASC";
	$r = mysql_query($q, $conn);
	
	$i = 2;
	while($row = mysql_fetch_assoc($r))
	{
		$excel->getActiveSheet()->SetCellValue('A' . $i, $row['purchase_date']);
		$excel->getActiveSheet()->SetCellValue('B' . $i, $row['invoice_id']);
		$excel->getActiveSheet()->SetCellValue('C' . $i, $row['rep']);
		$excel->getActiveSheet()->SetCellValue('D' . $i, $row['business']);
		$excel->getActiveSheet()->SetCellValue('E' . $i, $row['product_code']);
		$excel->getActiveSheet()->SetCellValue('F' . $i, $row['quantity']);
		$excel->getActiveSheet()->SetCellValue('G' . $i, '$' . $row['price']);
		$i++;
	}
	
	// Non-shipped invoices
	/*$excel->createSheet();
	$excel->setActiveSheetIndex($worksheet++);
	$excel->getActiveSheet()->setTitle("Not Shipped");
	$excel->getActiveSheet()->SetCellValue('A1', 'Purchase Date');
	$excel->getActiveSheet()->SetCellValue('B1', 'Invoice');
	$excel->getActiveSheet()->SetCellValue('C1', 'Business');
	$excel->getActiveSheet()->SetCellValue('D1', 'Product');
	$excel->getActiveSheet()->SetCellValue('E1', 'Count');
	$excel->getActiveSheet()->SetCellValue('F1', 'Price');
	
	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(34.71);
	
	$q = "SELECT 
		invoices.invoice_id, invoices.lead_id, invoices.rep_id, invoices.dialer_id, invoices.purchase_date,
		leads.contact, leads.business, leads.address, leads.city, leads.state, leads.zipcode, leads.phone,
		reps.name,
		items.*,
		products.*
		FROM invoices 
		LEFT JOIN leads USING(lead_id) 
		LEFT JOIN reps ON reps.id = invoices.rep_id
		INNER JOIN items USING(invoice_id)
		INNER JOIN products USING(product_id)
		WHERE purchase_date <= DATE_SUB(NOW(), INTERVAL 30 DAY) 
		AND ship_date = '0000-00-00'
                ORDER BY purchase_date ASC";
	$r = mysql_query($q, $conn);
	
	$i = 2;
	while($row = mysql_fetch_assoc($r))
	{
		$excel->getActiveSheet()->SetCellValue('A' . $i, $row['purchase_date']);
		$excel->getActiveSheet()->SetCellValue('B' . $i, $row['invoice_id']);
		$excel->getActiveSheet()->SetCellValue('C' . $i, $row['business']);
		$excel->getActiveSheet()->SetCellValue('D' . $i, $row['product_code']);
		$excel->getActiveSheet()->SetCellValue('E' . $i, $row['quantity']);
		$excel->getActiveSheet()->SetCellValue('F' . $i, '$' . $row['price']);
		$i++;
	}*/
	
	// Create document
	$writer = new PHPExcel_Writer_Excel2007($excel);
	$writer->save('reports.xlsx');
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename=reports.xlsx');
	header('Content-Length: ' . filesize('./reports.xlsx'));
	readfile('./reports.xlsx');
?>