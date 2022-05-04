<html>
	<head>
		<title>Sales</title>
		<style type="text/css">
			body {
				background-color:#012400;
				font-size:14px;
				font-family:Arial, Helvetica, sans-serif;
				width: 900px;
				color:#fff;
				margin:0 auto;
				padding:0;
				border:0;
				vertical-align:middle;
			}
			
			ul#tabs {
				list-style-type: none;
				height: 40px;
				width: 900px;
				padding: 0;
				/*margin: auto;*/
			}
			
			ul#tabs li {
				float: left;
			}
			
			ul#tabs a {
				background: #fff;
				padding-right: 32px;
				padding-left: 32px;
				display: block;
				line-height: 40px;
				text-decoration: none;
				font-family: Georgia, "Times New Roman", Times, serif;
				font-size: 18px;
				color: #371C1C;
			}
			
			ul#tabs a:hover {
				background: #045701;
			}
			
			a, a:hover {
				color: #ffffff
			}
			
			table {
				margin: auto;
				font-size: 14px;
			}
			
			.right {
				text-align: right;
			}
			
			.table tr:nth-child(even) {
				background: rgba(255, 255, 255, .2);
			}

			.table tr:nth-child(odd) {
				background: rgba(255, 255, 255, .4);
			}
			
			.autocomplete-w1 {
				position:absolute; 
				top:0px; 
				left:0px; 
				margin:6px 0 0 6px; 
				/* IE6 fix: */ 
				_background:none; 
				_margin:1px 0 0 0; 
			}
			
			.autocomplete { 
				border:1px solid #999; 
				background:#FFF; 
				cursor:default; 
				text-align:left; 
				max-height:350px; 
				overflow:auto; 
				margin:-6px 6px 6px -6px;
				color: #000;
				/* IE6 specific: */ 
				_height:350px;  
				_margin:0; 
				_overflow-x:hidden; 
			}
			
			.autocomplete .selected { 
				background:#F0F0F0; 
			}
			
			.autocomplete div { 
				padding:2px 5px; 
				white-space:nowrap; 
				overflow:hidden; 
			}
			
			.autocomplete strong { 
				font-weight:normal; 
				color:#3399FF; 
			}
			
			#simplemodal-overlay {
				background-color: #000;
			}
			
			#simplemodal-container {
				background-color: #000;
				opacity: 0.9;	
			}
/*dropdown menu*/	
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 230px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}
.dropdown-content a {
  color: black;
  /*padding: 12px 16px;*/
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {background-color: #ddd;}
.dropdown:hover .dropdown-content {display: block;}
/*dropdown menu*/
</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.numeric.js"></script>
<script type="text/javascript" src="js/stuff.js"></script>
<script type="text/javascript" src="js/jquery.simplemodal-1.4.2.js"></script>
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
</head>

	<body>
		<div style="float:right;">
			<img src="header_right.gif" /></br>
		</div>		

		<div style="clear:both;"></div> 
		<div style="width: 100%; display: inline-block; margin-bottom: 15px;">
		<?php
			include('config.php');  
			$q = "SELECT * FROM `users` WHERE `rep_id` = '".$_SESSION['rep_id']."' AND `username` = '".$_SESSION['user']."'";
			$r = mysql_query($q, $conn);
			$row =mysql_fetch_assoc($r);
			echo "Welcome " .$row['username'];
		?>
		<a href="logout.php">Logout</a> 
		<?php if($_SESSION['level'] == 1){ ?>
		| <a href="1099.php">1099 Orders</a><br />
		<?php } ?>
		</div>
		<ul id="tabs">
			<?php if($_SESSION['level'] == 1) { ?>
			<li><a href="index.php">Leads</a></li>
			<?php } ?>

			<?php if($_SESSION['level'] != 1) { ?>			
			<li class="dropdown">
				<a href="#">Call List</a>
				<div class="dropdown-content">
					<a href="list_self.php">My Clients</a>
					<?php if($_SESSION['access'] == 1) { ?>
    				<a href="list_house.php">House A/C Clients</a>
    				<a href="list_all.php">All Clients</a>
    				<?php }?>    				
  				</div>
			</li>
		    <?php } ?>
			<li><a href="clients.php">Clients</a></li>
			<li><a href="new_invoice.php">New Order</a></li>



			<?php if($_SESSION['level'] == 1) { ?>
			<li><a href="products.php">Products</a></li>
			<li><a href="reps.php">Sales Reps</a></li>
			<li><a href="misc.php">Misc</a></li>
			<li><a href="payroll.php">Payroll</a></li>
			<?php } ?>
		</ul>

		<!-- <ul id="tabs">
			<?php //if($_SESSION['level'] == 1) { ?>
			<li><a href="index.php">Leads</a></li>
			<?php //} ?>

			<li><a href="clients.php">Clients</a></li>
			<li><a href="products.php">Products</a></li>
			<li><a href="reps.php">Sales Reps</a></li>

			<?php //if($_SESSION['level'] == 1) { ?>
			<li><a href="new_invoice.php">New Order</a></li>
			<li><a href="misc.php">Misc</a></li>
			<li><a href="payroll.php">Payroll</a></li>
			<?php //} ?>
		</ul> -->
		