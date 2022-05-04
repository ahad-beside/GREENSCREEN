<?php
	include('../config.php');
	
	if(isset($_GET['invoice_id']) and is_numeric($_GET['invoice_id']))
	{
		$q = "SELECT invoices.*, leads.business
				FROM invoices 
				INNER JOIN leads USING(lead_id)
				WHERE invoice_id = " . mysql_real_escape_string($_GET['invoice_id']);
		$r = mysql_query($q, $conn);
		
		$return = "";
		if(mysql_num_rows($r) > 0)
		{
			$row = mysql_fetch_assoc($r);
			
			$total = 0.0;
			$q = "SELECT price FROM items WHERE invoice_id = $row[invoice_id]";
			$rr = mysql_query($q, $conn);
			while($row2 = mysql_fetch_assoc($rr))
				$total += $row2['price'];
			
			$return .= "<tr>";
			$return .= "<td><a href=\"#\" id=\"inv_$row[invoice_id]\" class=\"remove_invoice\">Remove</a><input type=\"hidden\" name=\"invoices[]\" value=\"$row[invoice_id]\" /></td>";
			$return .= "<td>$row[invoice_id]</td>";
			$return .= "<td>$row[business]</td>";
			$return .= "<td>$row[purchase_date]</td>";
			$return .= "<td>$" . number_format($total, 2) . "</td>";
			$return .= "</tr>";
		}
		else
		{
			$return = "error";
		}
		
		echo $return;
	}
?>