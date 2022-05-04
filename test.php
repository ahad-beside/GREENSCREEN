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
  

    $rep_id = array();
    if($_POST['rep_id'] == -1)
    {
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
      $leads = "SELECT *
            FROM leads
            LEFT JOIN reps ON reps.id = leads.rep_id
            WHERE last_pulled <= DATE_SUB(NOW(), INTERVAL 35 DAY)
            AND reps.id = 4 ORDER BY date DESC";
          $result = mysql_query($leads, $conn);

          echo "Rows: " . mysql_num_rows($result);
      /*if($week == 1)
      {
        $count = mysql_num_rows($result) / $maxWeeks;
        $qq = "UPDATE reps SET weekly_leads = " . $count . " WHERE id = " . $rep_id[$index];
        mysql_query($qq, $conn);
      }
      else
      {
        $qq = "SELECT weekly_leads FROM reps WHERE id = " . $rep_id[$index];
        $rr = mysql_query($qq, $conn);
        $rrr = mysql_fetch_assoc($rr);
        $count = $rrr['weekly_leads'];
      }*/
      
      $i = 0;
      $sales_rep = "";
      while($row2 = mysql_fetch_assoc($result)) {
        //if($i++ < $count) {
          $i++;
          // Purchase history
          $q = "SELECT invoices.invoice_id, DATE_FORMAT(invoices.purchase_date, '%c/%d/%y') AS purchase_date, invoices.ship_date, invoices.paid_date, items.quantity, items.price, products.product_code
              FROM invoices
              INNER JOIN items USING(invoice_id) 
              INNER JOIN products USING(product_id)
              WHERE lead_id = " . $row2['lead_id'] . 
              " ORDER BY invoices.purchase_date DESC
              LIMIT 1";
          $r = mysql_query($q, $conn);
          echo "Rows: " .mysql_num_rows($r);
          if(mysql_num_rows($r))
          {
            $rr = mysql_fetch_assoc($r);
          
            if($rr['ship_date'] == '0000-00-00' or $rr['paid_date'] == '0000-00-00')
            {
              $i--;
              continue;
            }
          }
          
          // Reset last_pulled so as to not pull this again for at least 30days
          $q = "UPDATE leads SET `last_pulled`=CURDATE() WHERE lead_id = " . $row2['lead_id'];
          mysql_query($q, $conn);
          
          $q = "INSERT INTO pull_history (lead_id) VALUES('$row2[lead_id]')";
          mysql_query($q, $conn);
          
          $sales_rep = str_replace(' ', '_', trim($row2['name']));
          echo "Spsn: _" . $row2['rep_id'] . "__ " . $row2['name'] . "_______________" . date('m/d/Y') . "___Property of G3 Graphics__", $paramsText;
          
          $t = stripslashes(htmlspecialchars(convert_smart_quotes($row2['business'])));
          echo $t, $paramsText;
          
          $t = stripslashes(htmlspecialchars(convert_smart_quotes($row2['address'], ENT_QUOTES)));
          echo $t . "\t\tPr " . $row2['phone'], $paramsText;
          
          echo htmlspecialchars(convert_smart_quotes($row2['city'])) . "\t" . $row2['state'] . " " . $row2['zipcode'] . ((!empty($row2['fax'])) ? "\tAl " . $row2['fax'] : ""), $paramsText;
          
          echo stripslashes(htmlspecialchars(convert_smart_quotes($row2['contact']))),  $paramsText;
          
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
            //echo "Invoice   Item\tQty\tAmt", $paramsText;
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
                
                echo $invoice1 . " ! " . $invoice2, $paramsText;
                $numLines++;
                
                if($lines > 2)
                {
                  for($k = 0; $k < $lines; $k++)
                  {
                    $ad1 = str_pad($ad_copy1[$k], $SPACES/2, " ", STR_PAD_BOTH);
                    $ad2 = str_pad($ad_copy2[$k], $SPACES/2, " ", STR_PAD_BOTH);
                    $t = $ad1 . (($ad1 == "") ? "\t\t" : "") . "\t! " . $ad2;
                    echo $t, $paramsText;
                    $numLines++;
                  }
                }
              }
              else
              {
                echo $row['invoice_id'] . "   " . $row['product_code'] . "\t" . $row['quantity'] . "\t" . $row['price'] . "\t " . $row['purchase_date'], $paramsText;
                $numLines++;
              }
            }
          }
          
          if($i % 2 == 0)
            $docx->addBreak('page');
          else
          {
            // Start next lead in the middle of the page
            $linebreaks = ($PAGE_LINES / 2) - $numLines;
            for($l = 0; $l < $linebreaks; $l++)
              $docx->addBreak('line');
            echo " - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ";
          }
        //}
        //else
        //  break;
      }
      $leadCount[] = $i;
      
      if($index+1 < count($rep_id))
        $docx->addBreak('page');
    }
    
    // Statistics
    
    
    $total = 0;
    for($i = 0; $i < count($rep_id); $i++)
    {
      $q = "SELECT * FROM reps WHERE id = " . $rep_id[$i];
      $r = mysql_query($q, $conn);
      $row = mysql_fetch_assoc($r);
      
      if($leadCount[$i] > 0)
      {
        echo $row['name'] . "\t" . $leadCount[$i], $paramsText;
        $total += $leadCount[$i];
      }
    }
    echo "TOTAL\t" . $total, $paramsText;
    
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

  

?>