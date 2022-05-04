<?php
	session_start();
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	include('config.php');
	include('header.php');
?>
<div id="content">
	<a href="reps.php">Back</a>
	<table class="table" cellpadding="3" cellspacing="3">
	<h2 style="text-align:center;">View All Rep User</h2>
	<tr>
		<th></th>
		<th></th>
		<th>ID</th>
		<th>Fullname</th>
		<th>User Name</th>
	</tr>
	
	<!-- <script>
    function deleteContent(id) {
        if(confirm('Are you sure you want to delete this ?')) {
        window.location='del_new_sales.php?id='+id;
        }
    	return false;
    } 
	</script> -->
	<?php
		
		$q = "SELECT * FROM users LEFT JOIN reps on users.rep_id=reps.id WHERE users.rep_id <> 0";
		$r = mysql_query($q, $conn);
		while($row = mysql_fetch_assoc($r)) {
	?>
	<tr>
		<!-- <td><a href="javascript:void(0)" onclick="return deleteContent('<?php //echo $row['user_id']; ?>');" type="button" id="delcont" >Delete</a></td> -->
		<td><a href="#" type="button" class="delete_comment" id="<?php echo $row['user_id']; ?>">Delete</a></td>
		<td><a href="edit_new_sales.php?id=<?php echo $row['user_id']; ?>">Edit</a></td>
		<td><?php echo $row['user_id']; ?></td>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $row['username']; ?></td>
	</tr>
	<?php } ?>
	</table>
</div>


</body>
</html>