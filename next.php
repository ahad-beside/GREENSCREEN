   
<?php
require_once('config.php');
$perpage = 2;
if(isset($_GET['page']) & !empty($_GET['page'])){
    $curpage = $_GET['page'];
}else{
    $curpage = 1;
}
$start = ($curpage * $perpage) - $perpage;
$PageSql = "SELECT * FROM `crud`";
$pageres = mysqli_query($connection, $PageSql);
$totalres = mysqli_num_rows($pageres);
 
$endpage = ceil($totalres/$perpage);
$startpage = 1;
$nextpage = $curpage + 1;
$previouspage = $curpage - 1;
 
$ReadSql = "SELECT * FROM `crud` LIMIT $start, $perpage";
$res = mysqli_query($connection, $ReadSql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple CRUD Application - READ Operation</title>
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
 
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >
 
<link rel="stylesheet" href="styles.css" >
 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
 
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 
 
</head>
<body>
<div class="container">
    <div class="row">
    <h2>Read Operation in CRUD applicaiton</h2>
        <table class="table "> 
        <thead> 
            <tr> 
                <th>#</th> 
                <th>Full Name</th> 
                <th>E-Mail</th> 
                <th>Age</th> 
                <th>Gender</th> 
                <th>Extras</th>
            </tr> 
        </thead> 
        <tbody> 
        <?php 
        while($r = mysqli_fetch_assoc($res)){
        ?>
            <tr> 
                <th scope="row"><?php echo $r['id']; ?></th> 
                <td><?php echo $r['first_name'] . " " . $r['last_name']; ?></td> 
                <td><?php echo $r['email_id']; ?></td> 
                <td><?php echo $r['gender']; ?></td> 
                <td><?php echo $r['age']; ?></td> 
                <td>
                    <a href="update.php?id=<?php echo $r['id']; ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                </td>
            </tr> 
        <?php } ?>
        </tbody> 
        </table>
    </div>
 
    <nav aria-label="Page navigation">
  <ul class="pagination">
  <?php if($curpage != $startpage){ ?>
    <li class="page-item">
      <a class="page-link" href="?page=<?php echo $startpage ?>" tabindex="-1" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">First</span>
      </a>
    </li>
    <?php } ?>
    <?php if($curpage >= 2){ ?>
    <li class="page-item"><a class="page-link" href="?page=<?php echo $previouspage ?>"><?php echo $previouspage ?></a></li>
    <?php } ?>
    <li class="page-item active"><a class="page-link" href="?page=<?php echo $curpage ?>"><?php echo $curpage ?></a></li>
    <?php if($curpage != $endpage){ ?>
    <li class="page-item"><a class="page-link" href="?page=<?php echo $nextpage ?>"><?php echo $nextpage ?></a></li>
    <li class="page-item">
      <a class="page-link" href="?page=<?php echo $endpage ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">Last</span>
      </a>
    </li>
    <?php } ?>
  </ul>
</nav>
</div>
 
</body>
</html>