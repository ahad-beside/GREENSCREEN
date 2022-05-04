<?php
	session_start();
	
	if(!isset($_SESSION['user']))
		header('Location: login.php');
	
	include('config.php');
	include('header.php');
?>
		<div id="content">
			<a href="new_rep.php">New Sales Rep</a><br /><br />
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
					$q = "SELECT * FROM reps";
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
				<?php 
					}
				?>
			</table>
		</div>
	</body>
</html>