<?php
	session_start();
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	include('config.php');
	include('header.php');
?>
<style type="text/css">
ol, ul {
    margin-top: 0;
    margin-bottom: 10px;
}
.pagination {
    display: inline-block;
    padding-left: 0;
    margin: 20px 0;
    border-radius: 4px;
}
.pagination > li {
    display: inline;
}
.pagination > li > a:focus, 
.pagination > li > a:hover, 
.pagination > li > span:focus, 
.pagination > li > span:hover {
    color: #23527c;
    z-index: 2;
    color: #23527c;
    background-color: #eee;
    border-color: #ddd;
}
.pagination > li:first-child > a, 
.pagination > li:first-child > span {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}
.pagination > li:last-child > a, 
.pagination > li:last-child > span {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
.pagination > li > a, 
.pagination > li > span {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #337ab7;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
    border-top-color: rgb(221, 221, 221);
    border-right-color: rgb(221, 221, 221);
    border-bottom-color: rgb(221, 221, 221);
    border-left-color: rgb(221, 221, 221);
}
.pagination > .active > a, 
.pagination > .active > a:focus, 
.pagination > .active > a:hover, 
.pagination > .active > span, 
.pagination > .active > span:focus, 
.pagination > .active > span:hover {
    z-index: 3;
    color: #fff;
    cursor: default;
    background-color: #337ab7;
    border-color: #337ab7;
}
</style>
<div id="content">
	<a href="new_rep.php">New Sales Rep</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a id="" href="new_sales_user.php">New Sales Rep User</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="view_sales_user.php">View All Rep User</a><br /><br />
	<table class="table" cellpadding="3" cellspacing="3">
	<tr>
		<th></th>
		<th>ID</th>
		<th>Name</th>
		<th>Front Commission</th>
		<th>Reload Comission</th>
		<th>Bonus Level</th>
		<th>Bonus %</th>
		<th>Below Par</th>
	</tr>
	<?php
	$limit = 15;  
    if (isset($_GET["page"])){ 
        $page  = $_GET["page"]; 
    } 
    else { 
        $page=1; 
    };  
    $start_from = ($page-1) * $limit;  
   
	$q = "SELECT * FROM reps ORDER BY id DESC LIMIT $start_from, $limit";
	$r = mysql_query($q, $conn);
	while($row = mysql_fetch_assoc($r)) {
?>
	<tr>
		<td><a href="edit_rep.php?id=<?php echo $row['id']; ?>">Edit</a></td>
		<td><?php echo $row['rep_id']; ?></td>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['front_comm']; ?>%</td>
		<td><?php echo $row['reload_comm']; ?>%</td>
		<td><?php echo $row['bonus_level']; ?></td>
		<td><?php echo $row['bonus_percentage']; ?>%</td>
		<td><?php echo $row['below_par']; ?>%</td>
	</tr>
	<?php } ?>
	</table>
	<?php
    $q = "SELECT COUNT(id) FROM reps";  
    $r = mysql_query($q, $conn);  
    $row = mysql_fetch_row($r);  
    $total_record = $row[0];  
    $total_pages = ceil($total_record / $limit);  
    $pagLink = "
    <nav aria-label='Page navigation' style='text-align:center;'>
  	<ul class='pagination'>
    <li>
      <a href='reps.php?page=1'>
        <span>&laquo;</span>
      </a>
    </li>
    "; 
            
    for($i=1; $i<=$total_pages; $i++): 
     
    //if($_GET['page']==$i)
	if(isset($_GET['page']) && $_GET['page']==$i)
        $active = 'active';
    else
        $active='';
        $pagLink .= "<li class='".$active."'><a href='reps.php?page=".$i."'>".$i."</a></li>";  
    endfor;
          
    echo $pagLink . "
    <li>
      <a href='reps.php?page=".$total_pages."'>
        <span>&raquo;</span>
      </a>
    </li>
  	</ul>
	</nav>";  
	?>
</div>

</body>
</html>