<?php
  include('config.php');

  $x = array(566,968,1271,1550,1551,2686,3764,4475,4503,4739,5407,7115,5988,6080,6232,6245,6534,6546,6643,6649,6893,6916,6969,7009,7178,7247,7368,7372,7420,7531,7581,7647,7659,7673);
  foreach ($x as $v) {
    $q = "SELECT invoices.invoice_id, DATE_FORMAT(invoices.purchase_date, '%c/%d/%y') AS purchase_date, invoices.ship_date, invoices.paid_date, items.quantity, items.price, products.product_code
      FROM invoices
      INNER JOIN items USING(invoice_id) 
      INNER JOIN products USING(product_id)
      WHERE lead_id = " . $v . 
      " ORDER BY invoices.purchase_date DESC
      LIMIT 1";
    $r = mysql_query($q, $conn);
    $rr = mysql_fetch_assoc($r);
    if($rr['ship_date'] == '0000-00-00' or $rr['paid_date'] == '0000-00-00')
    {
      echo "No Good <br>";
    } else {
      echo "Good <br>";
    }
  }
?>