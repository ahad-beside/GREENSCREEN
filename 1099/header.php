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
		</style>
		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.numeric.js"></script>
		<script type="text/javascript" src="js/stuff.js"></script>
		<script type="text/javascript" src="js/jquery.simplemodal-1.4.2.js"></script>
		<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
	</head>
	<body>
		<div style="float:right;">
			<img src="header_right.gif" />
		</div>
		<div style="clear:both;"></div>
		
		<a href="logout.php">Logout</a><br />
		<ul id="tabs">

			<li><a href="clients.php">Clients</a></li>
			<li><a href="products.php">Products</a></li>
			<?php if($_SESSION['level'] == 1) { ?>
			<li><a href="new_invoice.php">New Order</a></li>
			<?php } ?>
		</ul>
		