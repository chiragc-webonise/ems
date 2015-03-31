<?php
	session_start();
	$error = array();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	$loginEmpID = isset($_SESSION['empID']) ? $_SESSION['empID'] : '';
	if(isset($username)){
		$bindUser = array(
			':username' => $username
		);
		$loginDetails = $db->select('users', 'username = :username', $bindUser);
		if(!$loginDetails)
			header("Location: login.php");

		if(isset($_POST['updateProfile']))
		{
			echo "<br/><br/>";print_r($_POST);
			$name = trim($_POST['name']);
			$date_of_birth = trim($_POST['date_of_birth']);
			$gender = $_POST['gender'];
			$hire_date = trim($_POST['hire_date']);

			$validate_name = "/^[A-Za-z ]{3,128}$/";

			if(empty($name)) {
		      $error[] = 'Name cannot be blank.';
		      $_POST['name'] = '';
		   	} elseif (!preg_match($validate_name, $name)) {
		   		$error[] = "Only letters and space are allowed.";
		      	$_POST['name'] = '';
		   	}

		   	if(!empty($date_of_birth)) {
		   		$dob = explode('/', $date_of_birth);
		   		$month = strstr($dob[0], 'm');
		   		$day = strstr($dob[1], 'd');
		   		$year = strstr($dob[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect date of birth.";
			   		$_POST['date_of_birth'] = '';
			   	}
		   	}

		   	if(!empty($hire_date)) {
		   		$hDate = explode('/', $hire_date);
		   		$month = strstr($hDate[0], 'm');
		   		$day = strstr($hDate[1], 'd');
		   		$year = strstr($hDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect hiring date.";
			   		$_POST['hire_date'] = '';
			   	}
		   	}

		   	if(empty($error)) {
		   		// Update into Employee Table
		   		$updateBindEmp = array('id' => $loginEmpID);
		   		$updateEmp = array(
		   			'name' => $name,
		   			'gender' => $gender,
		   			'modified' => date('Y-m-d H:i:s')
	   			);
		   		if(!empty($date_of_birth)){
		   			$date_of_birth = date('Y-m-d',strtotime($date_of_birth));
		   			$updateEmp['date_of_birth'] = $date_of_birth;
		   		}
		   		if(!empty($hire_date)){
		   			$hire_date = date('Y-m-d',strtotime($hire_date));
		   			$updateEmp['hire_date'] = $hire_date;
		   		}

		   		$db->update('employees', $updateEmp, 'md5(id) = :id', $updateBindEmp);
		   		header("Location: profile.php");
		   	}
		}
	}
?>
<html>
<head>
<title>Employee Management System - Profile</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
<script src="/js/jquery-1.11.2.js"></script>
<script src="/js/jquery.inputmask.js"></script>
<script src="/js/jquery.inputmask.extensions.js"></script>
<script src="/js/jquery.inputmask.date.extensions.js"></script>
</head>
<script type="text/javascript">
	$(document).ready(function(){
		$('#managerList').hide();
		$("#date_of_birth").inputmask("mm/dd/yyyy");
		$("#hire_date").inputmask("mm/dd/yyyy");
	});
</script>
<body>

    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
		<div class="container">
			<ul class="nav navbar-nav navbar-center" style="text-align:center;" >
				<li><a href="employee.php">Employee</a></li>
				<li><a href="department.php">Departments</a></li>
				<li><a href="job_title.php">Job Titles</a></li>
				<li><a href="view_employees.php">View Employee List</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right" style="text-align:right;" >
				<li class="active"><a href="profile.php">Profile</a></li>
				<li style="text-align:right;"><a href="logout.php">Logout</a></li>
			</ul>
		</div>
	</nav>

	<div style="padding-top:40px;padding-left:50px;" class="container">
		<?php $bindEmpId = array(':empId' => $loginEmpID);
			$empDetails = $db->select('employees', 'md5(id) = :empId', $bindEmpId);
			if(empty($empDetails)): ?>
				<span style=\"font-size:20px;font-weight:bold;\">This is not your profile.</span>
				&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><a href="employee.php" class="btn btn-default">Back</a>
		<?php else: ?>
			<form name="registerForm" class="form-signin" method="post" enctype="multipart/form-data">
				<h2 class="form-signin-heading">Edit Profile</h2>
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
					<?php if(isset($_POST['name'])) : ?>
						<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '';?>">
					<?php else : ?>
						<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php echo isset($empDetails[0]['name']) ? $empDetails[0]['name'] : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<label class="radio-inline">
						<input type="radio" name="gender" id="isMale" value="male" <?php echo isset($empDetails[0]['gender']) ? (($empDetails[0]['gender'] == 'male') ? 'checked="true"' : '') : 'checked="true"';?>> Male
					</label>
					<label class="radio-inline">
						<input type="radio" name="gender" id="isFemale" value="female" <?php echo ($empDetails[0]['gender'] == 'female') ? 'checked="true"' : '';?>> Female
					</label><br/><br/>
				</div>
				<div>
					<?php if(isset($_POST['date_of_birth'])) : ?>
						<input type="text" name="date_of_birth" id="date_of_birth" class="form-control" placeholder="Date Of Birth" value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';?>">
					<?php else : ?>
						<input type="text" name="date_of_birth" id="date_of_birth" class="form-control" placeholder="Date Of Birth" value="<?php echo isset($empDetails[0]['date_of_birth']) ? date('m/d/Y',strtotime($empDetails[0]['date_of_birth'])) : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<?php if(isset($_POST['hire_date'])) : ?>
						<input type="text" name="hire_date" id="hire_date" class="form-control" placeholder="Date Of Hiring" value="<?php echo isset($_POST['hire_date']) ? $_POST['hire_date'] : '';?>">
					<?php else : ?>
						<input type="text" name="hire_date" id="hire_date" class="form-control" placeholder="Date Of Hiring" value="<?php echo isset($empDetails[0]['hire_date']) ? date('m/d/Y',strtotime($empDetails[0]['hire_date'])) : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<input type="submit" class="btn btn-lg btn-primary" value="Update" name="updateProfile" id="updateProfile" style="align:center;">
					&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="employee.php" class="btn btn-lg btn-default">Back</a>
				</div>
			</form>
		<?php endif; ?>
	</div>
	
	
</body>
</html>



