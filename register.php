<?php
	session_start();
	$error = array();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	if(isset($username)){
		$bind = array(
			':username' => $username
		);
		$result = $db->select('users', 'username = :username', $bind);
		if(!empty($result))
			header("Location: view.php");
	}
	if(isset($_POST['registerLogin']))
	{
		$name = trim($_POST['fullName']);
		$username = trim($_POST['username']);
		$passwd = trim($_POST["passwd"]);
		$retypePasswd = trim($_POST['retypePasswd']);

		$validate_name = "/^[A-Za-z &_]{3,128}$/";
		$validate_username = "/^[A-Za-z0-9_]{1,20}$/";

		if(empty($name)) {
	      $error[] = 'Name cannot be blank.';
	   	} elseif (!preg_match($validate_name, $name)) {
	   		$error[] = "Letters only.";
	   	}

	   	if(empty($username)) {
	      $error[] = 'Username cannot be blank.';
	   	} elseif (!preg_match($validate_username, $username)) {
	   		$error[] = "No special charaters allowed.";
	   	}

	   	if (empty($passwd)){
	        $error[] = "Password cannot be blank.";
	   	} elseif (strlen($passwd) < 6) {
	   		$error[] = "Minimum Password length is 5.";
	   	}

	   	if (empty($retypePasswd)){
	        $error[] = "Retype Password cannot be blank.";
	   	} elseif (strlen($retypePasswd) < 6) {
	   		$error[] = "Minimum Retype Password length is 5.";
	   	}

	   	if(!empty($passwd) && !empty($retypePasswd)) {
	   		if($passwd != $retypePasswd){
	   			$error[] = 'Passwords do not match. Please type again.';
	   		} else {
	   			//Verify if username  already exists
	   			$bind = array(
	   				':username' => $username
   				);
   				$oldRecord = $db->select('users', 'username = :username', $bind);
   				if(!empty($oldRecord)) {
   					$error[] = "Username already exists.";
   				} else {
   					$bind = array(
   						':admin' => 'admin'
					);
		   			$role = $db->select('user_roles','name = "admin"', $bind, 'id');
		   			$adminRole = $role[0]['id'];
		   			$params = array(
				   		'name' => $name,
				   		'username' => $username,
				   		'password' => md5($passwd),
				   		'role_id' => $adminRole,
				   		'created' => date('Y-m-d H:i:s')
			   		);
			   		$db->insert('users',$params);

			   		//Insert into employee table for admin
			   		$bindRole = array(':roleId' => $adminRole);
					$addCond = 'Order By id DESC limit 1';
			   		$userId = $db->select('users', 'role_id = :roleId', $bindRole, 'id', $addCond);
			   		if(!empty($userId)){
			   			$paramsEmp = array(
				   			'name' => $name,
				   			'user_id' => $userId[0]['id'],
					   		'created' => date('Y-m-d H:i:s')
			   			);
			   			$db->insert('employees',$paramsEmp);

			   			$bindEmp = array(':userID' => $userId);
			   			$empID = $db->select('employees', 'user_id = :userID', $bindEmp, 'id');
			   			$_SESSION["empID"] = $empID[0]['id'];
			   		}
			   		$_SESSION['username'] = $username;
			   		$_SESSION['isAdmin'] = $adminRole;
			   		header("Location: employee.php");
			   	}
	   		}
	   	}		
	}

?>

<html>
<head>
<title>Employee Management System - Register</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
</head>
<body>
<form name="registerForm" class="form-signin" method="post" enctype="multipart/form-data">
	<h2 class="form-signin-heading">Register</h2>
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
		<input type="text" name="fullName" id="fullName" class="form-control" placeholder="Name" value="<?php echo isset($_POST['fullName']) ? $_POST['fullName'] : '';?>">
		<br/>
	</div>
	<div>
		<input type="text" id="username" name="username" class="form-control" placeholder="Username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '';?>">
		<br/>
	</div>
	<div>
		<input type="password" id="passwd" name="passwd" class="form-control" placeholder="Password">
		<br/>
	</div>
	<div>
		<input type="password" id="retypePasswd" name="retypePasswd" class="form-control" placeholder="Retype Password">
		<br/>
	</div>
	<div>
		<input type="submit" class="btn btn-lg btn-primary" value="Register" name="registerLogin" id="registerLogin" style="align:center;">
		&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="index.php" class="btn btn-lg btn-default">Back</a>
	</div>
</form>
</body>
</html>