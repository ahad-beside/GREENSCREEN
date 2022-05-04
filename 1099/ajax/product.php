<?php
	session_start();
	
	include('../config.php');
	
	$return = "";
	if(isset($_POST['product_type']) and is_numeric($_POST['product_type']))
	{
		$product_type = mysql_real_escape_string($_POST['product_type']);
		
		$q = "SELECT * FROM products WHERE active = 1 AND product_type = " . $product_type;
		$r = mysql_query($q, $conn);
		
		$i = 0;
		while($row = mysql_fetch_assoc($r)) 
		{
			
			$return .= "<tr>\r\n";
			$return .= ($_SESSION['level'] == 1) ? "<td valign=\"top\"><a href=\"edit_product.php?id=$row[product_id]\" class=\"edit_product\" id=\"$row[product_code]\">Edit</a><br />\r\n" : "<td></td>";
			$return .= "<a href=\"#\" id=\"$row[product_id]\" class=\"delete_product\">Delete</a></td>\r\n";
			$return .= "<td valign=\"top\" style=\"text-align:center;\">$row[product_code]<br /><p style=\"font-size:10pt;\">$row[description]</p></td>\r\n";
			$return .= "<td style=\"text-align:center;\"><table cellpadding=\"5\">\r\n";
			$return .= "
				<tr>
					<th>Quantity</th>
					<th>Unit Price</th>
					<th>Extended Price</th>
				</tr>
			";
			
			$q = "SELECT * FROM prices WHERE product_id = '$row[product_id]'";
			$rr = mysql_query($q, $conn);
			while($price = mysql_fetch_assoc($rr))
			{
				$return .= "
					<tr>
						<td>$price[quantity]</td>
						<td>$$price[unit_price]</td>
						<td>$$price[extended_price]</td>
					</tr>
				";
			}
			$return .= "</td></table>\r\n";
			$return .= "</tr>\r\n";
		}
	}
	
	echo $return;
?>