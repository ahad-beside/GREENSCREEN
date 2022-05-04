<?php
	$host = "localhost";
	// $user = "thinglet_g3graph";
	// $pass = "GeeK12MnY";
	// $dbname = "thinglet_g3graphics";
  $user = "root"; 
  $pass = "";
  $dbname = "greenscreen";
	
	$conn = mysql_connect($host, $user, $pass) or die("Could not connect to database.");
	mysql_select_db($dbname) or die("Could not select database.");

	$week = 5;



	$maxWeeks = 5;
?>