<?php 
  session_start();
  
  if(!isset($_SESSION['user']))
    header('Location: login.php');
    
  include('config.php'); 
  include('header.php'); 

  if (isset($_POST['formtype'])) {
    if (isset($_POST['invoiceid'])) {
      $newsql = "UPDATE leads_1099 SET status='".$_POST['status']."' WHERE invoice_id = ".$_POST['invoiceid'];
      mysql_query($newsql, $conn);
      echo "<h1>".mysql_error()."</h1>";
    }
  }
?>
    <div style="padding-bottom: 50px;">
      <table id="leads" class="table" cellpadding="3" cellspacing="3" style="width: 100%; font-size:12px;">

        <tr>
          <th></th>
          <th>Order #</th>
          <th>Contact</th>
          <th>Address</th>
          <th>City</th>
          <th>State</th>
          <th>Phone</th>
          <th>Billing</th>
          <th>Status</th>
          <th></th>
        </tr>
        <?php
          $q = "SELECT * FROM leads_1099 ORDER BY date ASC";
          $r = mysql_query($q, $conn);
          
          while($row = mysql_fetch_assoc($r))
          { 
            $sales_q = "SELECT * FROM invoices_1099 where invoice_id = " . $row['invoice_id'] . " ORDER BY purchase_date DESC";
            $sales = mysql_query($sales_q, $conn);
            
            $purchase_date = "-";
            if(mysql_num_rows($sales))
            {
              $date = mysql_fetch_assoc($sales);
              $date = $date['purchase_date'];
            }
            echo "<h1>".mysql_error()."</h1>";
        ?>
        <tr>
          <td></td>
          <td><?php echo $row['invoice_id']; ?><a href="#"> Details..</a></td>
          <td><?php echo $row['contact']; ?></td>
          <td><?php echo $row['address']; ?></td>
          <td><?php echo $row['city']; ?></td>
          <td><?php echo $row['state']; ?></td>
          <td><?php echo $row['phone']; ?></td>
          <td>
<?php 
          $q = "SELECT * FROM billing_1099 WHERE invoice_id = ".$row['invoice_id'];
          $bill = mysql_query($q, $conn);
if (mysql_num_rows($bill) <= 0) {
  echo "<a href='1099/billing.php?id=".$row['invoice_id']."'>Add Info</a>";
} else {
  echo "<a href='1099/billing.php?add=".$row['invoice_id']."'>View Info</a>";
}
?>
          </td>
          <td>            
          <form method="POST" action="1099.php">
          <input type="hidden" name="formtype" value="1">
          <input type="hidden" name="invoiceid" value="<?php echo $row['invoice_id']; ?>">
            <select name="status" onchange="this.form.submit();">
            <?php 
            $options = array("", "Pending", "Confirmed", "Cancelled");
            
            foreach ($options as $option) {
              if ($row['status'] == $option) {
                echo "<option value='".$option."' selected='selected'>".$option."</option>";
              } else {
                echo "<option value='".$option."'>".$option."</option>";
              }
            }
            ?>
            </select>
          </td>
          <td></td>
        </tr>
        <?php
          }
        ?>
      </table>
    </div>
  </body>
</html>