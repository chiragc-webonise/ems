<?php
	session_start();
	$error = array();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$header = dirname(__FILE__)."/config/header.php";
	include $header;
	$db = new db("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
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


		if(isset($_POST['addDepartment']))
		{
			$name = trim($_POST['name']);
			$validate_name = "/^[A-Za-z &_]{3,128}$/";

			if(empty($name)) {
		      $error[] = 'Name cannot be blank.';
		   	} elseif (!preg_match($validate_name, $name)) {
		   		$error[] = "Only letters, space and characters i.e. & _  are allowed.";
		   	} else {
		   		//Verify if department name already exists
	   			$bind = array(
	   				':name' => $name
   				);
   				$oldRecord = $db->select('departments', 'name = :name', $bind);
   				if(!empty($oldRecord)) {
   					$error[] = "Department Name already exists.";
   				} else {
   					$params = array(
				   		'name' => $name,
				   		'created' => date('Y-m-d H:i:s')
			   		);
			   		$db->insert('departments',$params);
			   		header("Location: department.php");
			   	}
		   	}		
		}
	}
?>
<html>
<head>
<title>Employee Management System - Department</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
</head>
<body>

    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
		<div class="container">
			<ul class="nav navbar-nav navbar-center" style="text-align:center;" >
				<li><a href="employee.php">Employee</a></li>
				<li class="active"><a href="department.php">Departments</a></li>
				<li><a href="job_title.php">Job Titles</a></li>
				<li><a href="view_employees.php">View Employee List</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right" style="text-align:right;" >
				<li><a href="profile.php">Profile</a></li>
				<li style="text-align:right;"><a href="logout.php">Logout</a></li>
			</ul>
		</div>
	</nav>

	<div style="padding-top:40px;padding-left:50px;" class="container">
		<?php if(!$isAdmin): ?>
			<span style=\"font-size:20px;font-weight:bold;\">You are not authorized to view this page.</span>
			&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><a href="department.php" class="btn btn-default">Back</a>
		<?php else: ?>
		<form name="registerForm" class="form-signin" method="post" enctype="multipart/form-data">
			<h2 class="form-signin-heading">Add Department</h2>
			<br/>
			<?php
				if(!empty($error)) {
					foreach($error as $key=>$value){
						echo '<li><span class="errorText" style="color:red;">' . $value.'</span><br/>';
					}
					echo '<br/>';
				}
			?>
			<div>
				<input type="text" name="name" id="name" class="form-control" placeholder="Department Name">
				<br/>
			</div>
			<div>
				<input type="submit" class="btn btn-lg btn-primary" value="Add" name="addDepartment" id="addDepartment" style="align:center;">
				&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="department.php" class="btn btn-lg btn-default">Back</a>
			</div>
		</form>
		<?php endif;?>
	</div>
	
	
</body>
</html>



