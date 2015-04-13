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
		$bindUser = array(
			':username' => $username
		);
		$loginDetails = $db->select('users', 'username = :username', $bindUser);
		if(!$loginDetails)
			header("Location: login.php");

		if(isset($_POST['addJobTitle']))
		{
			$title = trim($_POST['title']);
			$validate_title = "/^[A-Za-z ]{3,128}$/";

			if(empty($title)) {
		      $error[] = 'Job Title cannot be blank.';
		   	} elseif (!preg_match($validate_title, $title)) {
		   		$error[] = "Only letters and space are allowed.";
		   	} else {
		   		//Verify if Job Title already exists
	   			$bind = array(
	   				':title' => $title
   				);
   				$oldRecord = $db->select('job_titles', 'title = :title', $bind);
   				/*var_dump($oldRecord);die;*/
   				if(!empty($oldRecord)) {
   					$error[] = "Job Title already exists.";
   					echo "aeaweA";
   					var_dump($oldRecord);die;
   				} else {
   					$params = array(
				   		'title' => $title,
				   		'created' => date('Y-m-d H:i:s')
			   		);
			   		/*var_dump($params);die;*/
			   		$db->insert('job_titles',$params);
			   		header("Location: job_title.php");
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
				<li><a href="department.php">Departments</a></li>
				<li class="active"><a href="job_title.php">Job Titles</a></li>
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
			&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><a href="job_title.php" class="btn btn-default">Back</a>
		<?php else: ?>
		<form name="registerForm" class="form-signin" method="post" enctype="multipart/form-data">
			<h2 class="form-signin-heading">Add Job Title</h2>
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
				<input type="text" name="title" id="title" class="form-control" placeholder="Job Title">
				<br/>
			</div>
			<div>
				<input type="submit" class="btn btn-lg btn-primary" value="Add" name="addJobTitle" id="addJobTitle" style="align:center;">
				&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="job_title.php" class="btn btn-lg btn-default">Back</a>
			</div>
		</form>
		<?php endif;?>
	</div>
	
	
</body>
</html>



