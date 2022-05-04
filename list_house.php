<?php 
	session_start();
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	include('config.php');
	include('header.php');
	$rep_id = $_SESSION['rep_id'];
	$currentDate = date("Y-m-d");
?>
<script type="text/javascript">
	$(function() {
	$('#client').focus();
});
</script>

<table cellpadding="3" cellspacing="3" style="width: 100%;">
	<?php

	$nsql = "SELECT COUNT(*) AS nrow FROM leads WHERE `call` != '3' AND (`marks_data` = '0000-00-00' OR `marks_data` <= '$currentDate') AND rep_id = '28' ORDER BY `lead_id`";
    $nresult = mysql_query($nsql, $conn);
    $fetchn = mysql_fetch_array($nresult);
    $ncount = $fetchn['nrow'];

	if($_GET['lead_id']!=''){
		$sql = "SELECT leads.*, reps.name,reps.rep_id AS code FROM leads INNER JOIN reps ON reps.id = leads.rep_id WHERE leads.call != '3' AND (leads.`marks_data` = '0000-00-00' OR leads.`marks_data` <= '$currentDate') AND leads.rep_id = '28' and leads.lead_id=".intval($_GET['lead_id'])." ORDER BY leads.`lead_id` ASC limit 1";
	}else{
    	$sql = "SELECT leads.*, reps.name,reps.rep_id AS code FROM leads INNER JOIN reps ON reps.id = leads.rep_id WHERE leads.call != '3' AND (leads.`marks_data` = '0000-00-00' OR leads.`marks_data` <= '$currentDate') AND leads.rep_id = '28' ORDER BY leads.`lead_id` ASC limit 1";
    }
    $result = mysql_query($sql, $conn);
    $fetch = mysql_fetch_array($result);
    
    $presql = "SELECT lead_id from leads where lead_id<". $fetch['lead_id'] ." and leads.call != '3' AND (leads.`marks_data` = '0000-00-00' OR leads.`marks_data` <= '$currentDate') AND leads.rep_id = '28' order by lead_id desc limit 1";
    $resultp = mysql_query($presql, $conn);
    $fetchp = mysql_fetch_array($resultp);

    $nextsql = "SELECT lead_id from leads where lead_id>". $fetch['lead_id'] ." and leads.call != '3' AND (leads.`marks_data` = '0000-00-00' OR leads.`marks_data` <= '$currentDate') AND leads.rep_id = '28' order by lead_id ASC limit 1";
    $resultn = mysql_query($nextsql, $conn);
    $fetchn = mysql_fetch_array($resultn);

     	$address = $fetch['address'];
       	$state = $fetch['state'];
       	$city = $fetch['city'];
       	$zipcode = $fetch['zipcode'];
        $business = $fetch['business'];
        $contact = $fetch['contact'];
        $email = $fetch['email'];
        $comment = $fetch['comment'];
        $phone = $fetch['phone'];
        $phone3 = $fetch['phone3'];
        $lead_id = $fetch['lead_id'];
        $rep_id = $fetch['rep_id'];
        $rep_name = $fetch['name'];
        $code = $fetch['code'];
        $add = $address.", ".$city.", ".$state." ".$zipcode
    ?>
<tr>	
	<td colspan="3" style="width:20px;">
	<div style="text-align: center;">
		<b style="font-size: 20pt;"><?php echo $business; ?></b></br>
			<center>Total:<?php echo $ncount; ?>&nbsp;#&nbsp;Lead Owner:&nbsp;<?php echo $rep_name ;?></center>
		<a style="float: right; margin-top:-50px;" id="modalForm2" href="#modelForm2<?php echo $lead_id ;?>">
			<button type="button"<?php if ($rep_id =='28') {echo "disabled='disabled'";} ; ?>>Update Client</button>
		</a>		
	</div>
	</td>
</tr>
<tr><td colspan="3"><hr /></td></tr>
<tr>
	<td width="40%">Contact:&nbsp;&nbsp;<b style="font-size: 15px;"><?php echo $contact ;?></b></td>
	<td width="30%">Email:&nbsp;&nbsp;<b style="font-size:15px;"><?php echo $email ;?></b></td>
	<td width="30%">Phone:&nbsp;&nbsp;<b style="font-size:15px;"><?php echo $phone ;?></b></td>
</tr>
<tr><td colspan="6"><hr /></td></tr>
<tr>	
	<td>Phone2:&nbsp;&nbsp;<label name="" value=""><?php echo $phone2; ?></label></td>
	<td>Phone3:&nbsp;&nbsp;<label name="" value=""><?php echo $phone3; ?></label></td>
	<td></td>
</tr>  
<tr><td colspan="3"><hr /></td></tr>
<tr>
	<td colspan="3" style="width:20px;">
	Address:&nbsp;&nbsp;<b id="addr" style="font-style:italic;"><?php echo $add; ?></b></br>
	</td>
</tr>
<tr><td colspan="3"><hr /></td></tr>
<tr><td colspan="3">Comment: <?php echo $comment; ?></td></tr>         
<tr><td colspan="3"><hr /></td></tr>

</table>

<div style="text-align: center;">
	<form method="GET" action="">
        <div id="div_pagination">
        	<div style="float: left; margin-bottom: 10px; margin-top: -8px;">
        	<button type="button" class="house_call" id="<?php echo $lead_id; ?>" name="but_call" <?php if ($rep_id =='28') {echo "disabled='disabled'";} ; ?>>Delete</button><?php ?>
 			<button type="button" class="house_sell" id="<?php echo $lead_id; ?>" name="but_sell" <?php if ($rep_id =='28') {echo "disabled='disabled'";} ; ?>>Sale/No Sale</button>                
           	</div>
        	<div style="float: right; margin-bottom: 10px; margin-top: -8px;">
        	
        	<ul class="pagination">
        		<?php if($fetchp['lead_id']!=''):?>
        			<a href="list_house.php?lead_id=<?= $fetchp['lead_id']?>"><button type="button">Previous</button></a>
        		<?php endif;?>
        		<?php if($fetchn['lead_id']!=''):?>
        		<a href="list_house.php?lead_id=<?= $fetchn['lead_id']?>"><button type="button">Next</button></a>
        		<?php endif;?>
			</ul>
			
	       	</div>
        </div>
    </form>
</div>

<div id="content">
<table id="invoices" class="table" cellpadding="3" cellspacing="3" style="width: 100%; text-align:center; border: 1px solid #fff;">
<tr>
	<td colspan="5">
	<h2><?php echo "PURCHASE HISTORY"; ?>
		<h3 style="text-align:center;"><?php echo stripslashes($business); ?></h3>
	</h2>
	</td>					
</tr>
<tr>
	<th>Invoice</th>
	<th>Purchase Date</th>
	<th>Product Code</th>
	<th>Quatity(Qty)</th>
	<th>Purchase Total</th>
</tr>
<?php

	$q = "SELECT * FROM `invoices` WHERE `lead_id` = $lead_id ORDER BY `purchase_date` DESC"; 
	$r = mysql_query($q, $conn);
	while ($row = mysql_fetch_assoc($r)){
		$q2 = "SELECT * FROM items WHERE invoice_id=$row[invoice_id]";
		$r2 = mysql_query($q2, $conn);
		
		$total = 0.0;
		while($row2 = mysql_fetch_assoc($r2)){
			$total += $row2['price'];
			$idd	= $row2['product_id'];

			$q3 = "SELECT * FROM products INNER JOIN product_type USING(product_type) where product_id = '$idd' ORDER BY name ASC";
			$r3 = mysql_query($q3, $conn);
			$row3 = mysql_fetch_assoc($r3);
?>
<tr>
	<td><a href="edit_invoice.php?id=<?php echo $row['invoice_id']; ?>" target="_blank"><?php echo $row['invoice_id']; ?></a></td>
	<td><?php echo $row['purchase_date']; ?></td>

	<td><?php echo $row3['product_code']." - ". stripcslashes($row3['name']); ?></td>

	<td><?php echo $row2['quantity']; ?></td>
	<td><?php echo "$" . number_format($total, 2); ?></td>
</tr>
<?php }} ?>
</table>
</div>


<div id="clientForm2" style="display:none; width:600px;">
<form name="newClient" id="#modelForm2<?php echo $lead_id ;?>" method="post" action="">
	<h2 style="text-align:center;">Update Client</h2>
	<table cellpadding="3" cellspacing="3" style="margin:auto;">
	<tr>
		<td><input type="hidden" name="lead_id" id="lead_id" value="<?php echo $lead_id; ?>"></td>
		<td colspan="2" style="text-align:center;"><b id="msg"></b></td>
	</tr>
	<tr>
		<td class="right">*Business</td>
		<td><input class="required" type="text" name="business" id="business" size="30" value="<?php echo $business; ?>" /></td>
	</tr>
	<tr>
		<td class="right">*Contact</td>
		<td><input class="required" type="text" name="contact" id="contact" value="<?php echo $contact; ?>" /></td>
	</tr>
	<tr>
		<td class="right">*Address</td>
		<td><input class="required" type="text" name="address" id="address" size="30" value="<?php echo $address; ?>" /></td>
	</tr>
	<tr>
		<td class="right">*City</td>
		<td><input class="required" type="text" name="city" id="city" value="<?php echo $city; ?>" /></td>
	</tr>
		<tr>
		<td class="right">*State</td>
		<td><input class="required" type="text" name="state" id="state" size="2" value="<?php echo $state; ?>" /></td>
	</tr>
	<tr>
		<td class="right">*Zipcode</td>
		<td><input class="required" type="text" name="zipcode" id="zipcode" size="5" value="<?php echo $zipcode; ?>" /></td>
	</tr>
	<tr>
		<td class="right">*Phone #1</td>
		<td><input class="required" type="text" name="phone1" id="phone1" size="12" value="<?php echo $phone; ?>" /></td>
	</tr>
	<tr>
		<td class="right">Phone #2</td>
		<td><input type="text" name="phone2" id="phone2" size="12" value="<?php echo $phone2;  ?>" /></td>
	</tr>
	<tr>
		<td class="right">Phone #3</td>
		<td><input type="text" name="phone3" id="phone3" size="12" value="<?php echo $phone3;?>" /><td>
	</tr>
	<tr>
		<td class="right">*Sales Rep</td>
		<td>
			<?php if($_SESSION['level'] == 1) { ?>
				<select name="rep" id="rep" class="required">
			<?php
				$q = "SELECT * FROM `reps`";							
				$r = mysql_query($q, $conn);
				while($row = mysql_fetch_assoc($r)) {
			?>
				<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
				</select>
			<?php } } ?>

			<select name="rep" id="rep" class="required">
				<option value="<?php echo $rep_id; ?>"><?php echo $rep_name; ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="right">Email</td>
		<td><input type="text" name="email" id="email" size="30" value="<?php echo $email; ?>" /></td>
	</tr>
	<tr>
		<td valign="top" class="right">Comment</td>
		<td><textarea name="comment" id="comment" cols="30" rows="5" value="<?php echo $comment; ?>"></textarea></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;"><input type="button" id="newClientModal2" name="submitButton" value="Update Client" />&nbsp;&nbsp;
		<input type="button" id="closeModal" name="cancelButton" value="Cancel" /></td>	
	</tr>
	</table>
	</form>
	</div>
	
	</body>
</html>
