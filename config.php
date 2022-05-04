<?php

  error_reporting(0);
  $host = "localhost";
  $user = "root";
  $pass = "";
  $dbname = "greenscreen";
	
  $conn = mysql_connect($host, $user, $pass) or die("Could not connect to database.");
  mysql_select_db($dbname) or die("Could not select database Name.");

	$week = 1;
  $maxWeeks = 5;
  
?>