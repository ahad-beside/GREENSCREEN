<?php
	session_start();
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	include('config.php');
	include('header.php');
?>
		<div id="sidebar" style="float:left; width: 19%;">
			<ul>
			<?php
				$q = "SELECT * FROM product_type ORDER BY name ASC";
				$r = mysql_query($q, $conn);
				
				while($row = mysql_fetch_assoc($r)) {
			?>
				<li><a href="#" class="product" id="<?php echo $row['product_type']; ?>"><?php echo $row['name']; ?></a></li>
			<?php
				}
			?>
			</ul>
		</div>
		
		<div id="content" style="float: right; width: 80%;">
			<?php if($_SESSION['level'] == 1) { ?>
			<a id="new_prod" href="new_product.php">New Product</a><br /><br />
			<?php } ?>
			
			<input id="prod_type2" type="hidden" name="product_type" value="" />
			<table id="products" class="table" style="width:100%;" cellpadding="5" cellspacing="1">
				<tr>
					<th style="width:5%;"></th>
					<th style="width:30%;" class="edit">Product Code</th>
					<th>Prices</th>
				</tr>
			</table>
		</div>
	</body>
</html>