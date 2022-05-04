<style type="text/css">
.flow{
	height:350px; 
	overflow-y:scroll;
	width: 500px;
}
</style>

<?php
	include('../config.php');
	//echo $_POST['current_rep'];	//onchange="this.form.submit();"	

	if(isset($_POST['current_rep'])){
		
		$current_rep = mysql_real_escape_string($_POST['current_rep']);
		$q = "SELECT lead_id, business, contact FROM leads WHERE rep_id = $current_rep ORDER BY lead_id DESC";
		$r = mysql_query($q, $conn);
		$num = mysql_num_rows($r);
		if($i<$num){
			$style = "flow";
		}
				
		$return = '';
		echo "<div class=\"$style\"> <table class=\"table\" id=\"myTable\">"; 
		echo "<tr><td><input type=\"checkbox\" name=\"check[$row[lead_id]]\" id=\"select_all\" onclick=\"toggle(this);\" ></td><td colspan=\"3\">Filter:<br/><input type=\"text\" placeholder=\"Search For Contact\" name=\"search\" id=\"myInput\" onkeyup=\"myFunction()\"/></td></tr>";
		echo "<th></th>"; 
		echo "<th>ID</th>"; 
		echo "<th>Contact</th>";
		echo "<th>Business</th>";

		while($row = mysql_fetch_assoc($r)) 
		{
			$return .= "<tr>\r\n";
			$return .= "<td style=\"text-align:center;\"><input type=\"checkbox\" name=\"check[$row[lead_id]]\" id=\"checkme\" value=\"check[$row[lead_id]]\"></td>\r\n";
			$return .= "<td valign=\"top\" style=\"text-align:center;\">$row[lead_id]</td>\r\n";
			$return .= "<td valign=\"top\" style=\"text-align:center;\">$row[contact]</td>\r\n";
			$return .= "<td valign=\"top\" style=\"text-align:center;\">$row[business]</td>\r\n";
			$return .= "</tr>\r\n";
		}
		echo $return;
		echo "</div></table>";
	}


?>