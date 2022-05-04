<?php

// function OpenCon(){
//   $dbhost = "localhost";
//   $dbuser = "root";
//   $dbpass = "";
//   $dbname = "greenscreen";
//   $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname) or die("Connect failed: %s\n". $conn -> error);
//   return $conn;
// }
//  function CloseCon($conn){
//   $conn -> close();
// }
include('conf.php');
?>
<table style="width: 100%; text-align:center; border: 1px solid #fff;">
<tr>
	<th>Product Code</th>
	<th>Name</th>
</tr>

<?php
	//$conn = OpenCon();
	$q = "SELECT * FROM products INNER JOIN product_type USING(product_type) where product_id = '30' ORDER BY name ASC";
	$r = mysql_query($q, $conn); 
	while($row = mysql_fetch_assoc($r)){
?>
<tr>
	<td><?php echo $row['product_code']; ?></td>
	<td><?php echo $row['name']; ?></td>
</tr>
<?php } ?>
</table>