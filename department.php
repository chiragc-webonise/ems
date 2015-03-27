<?php
	session_start();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : '0';
	$loginDetails = array();
	if(isset($username)){
		$bind = array(
			':username' => $username
		);
		$loginDetails = $db->select('users', 'username = :username', $bind);
		if(!$loginDetails)
			header("Location: login.php");
	}
?>
<html>
<head>
<title>Employee Management System - Department</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
<script src="/js/jquery-1.11.2.js"></script>
</head>
<script type="text/javascript">
	$(document).ready(function(){
		$('.edit-department').click(function(){
			var id = $(this).attr('id');
			window.location = 'edit_department.php?id='+id;
		});
	});
</script>
<body>
    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
		<div class="container">
			<ul class="nav navbar-nav navbar-center" style="text-align:center;" >
				<li><a href="employee.php">Employee</a></li>
				<li class="active"><a href="department.php">Departments</a></li>
				<li><a href="job_title.php">Job Titles</a></li>
				<li style="text-align:right;"><a href="logout.php">Logout</a></li>
			</ul>
		</div>
	</nav>

	<div style="padding-top:40px;padding-left:50px;" class="dptmnt">
		<?php if(!empty($loginDetails)) { ?>
			<span class="dptmnt-details"> Department Details</span><br/><br/>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th style="text-align:center;">#</th>
						<th style="text-align:center;">Name</th>
						<th style="text-align:center;">Created On</th>
						<?php if($isAdmin): ?>
							<th style="text-align:center;"></th>
						<?php endif;?>
					</tr>
				</thead>
				<tbody>
					<?php $departmentDetails = $db->select('departments');
					if(!empty($departmentDetails)) :
			    		foreach($departmentDetails as $key => $value): ?>
							<tr>
								<td style="text-align:center;"><?php echo ($key+1);?></td>
								<td style="text-align:center;" class="col-md-6"><?php echo $value['name'];?></td>
								<td style="text-align:center;" class="col-md-3"><?php echo date('d M Y',strtotime($value['created']));?></td>
								<?php if($isAdmin): ?>
									<td style="text-align:center;" class="col-md-1"> 
										<a class="edit-department" id="<?php echo md5($value['id']);?>">
								          <span class="glyphicon glyphicon-pencil"></span> 
								        </a>
									</td>
								<?php endif;?>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan=4> <i> No results found!</i></td>
						</tr>
					<?php endif; ?>	
				</tbody>
			</table>
			<?php if($isAdmin): ?>
				<a href="add_department.php" style="float:right;" class="btn btn-default">Add Department</a>
			<?php endif;?>
		<?php }?>	
	</div>
	
	
</body>
</html>



