<?php
	$error = array();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$header = dirname(__FILE__)."/config/header.php";
	include $header;
	$db = new db("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
	session_start();
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	if(isset($username)){
		$bind = array(
			':username' => $username
		);
		$result = $db->select('users', 'username = :username', $bind);
		if(!empty($result))
			header("Location: index.php");
	}
	if(isset($_POST['submitLogin']))
	{

		$validate_username = "/^[A-Za-z0-9_]{1,20}$/";

		$username = trim($_POST['username']);
		$passwd = trim($_POST["passwd"]);

		if(empty($username)) {
	      $error[] = 'Username cannot be blank.';
	   	} elseif (!preg_match($validate_username, $username)) {
	   		$error[] = "No special charaters allowed.";
	   		$_POST['username'] = "";
	   	}

	   	if (empty($passwd)){
	        $error[] = "Password cannot be blank.";
	   	}
	   	if(!empty($username) && !empty($passwd)) {
		   	$bind = array(
		   		'username' => $username,
		   		'password' => md5($passwd)
	   		);
	   		$results = $db->select('users','username = :username AND password = :password',$bind);
	   		if(empty($results)) {
	   			$error[] = "Invalid username or password.";
	   		} else {
	   			$bind = array(':id' => $results[0]['role_id'], ':name' => 'admin');
	   			$checkRole = $db->select('user_roles', 'id = :id AND name = :name', $bind);
	   			$bindEmp = array(':userID' => $results[0]['id']);
	   			$empID = $db->select('employees', 'user_id = :userID', $bindEmp, 'id');
	   			$_SESSION["username"] = $username;
	   			$_SESSION["empID"] = md5($empID[0]['id']);
	   			if(!empty($checkRole))
			   		$_SESSION['isAdmin'] = $results[0]['role_id'];
	   			header("Location: employee.php");
	   		}
	   	}		
	}
	
?>

<html>
<head>
<title>Employee Management System - Login</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
</head>
<body>
	<form name="loginForm" class="form-signin" method="post"> <!-- action="#" method="post" onsubmit="return validateForm()" -->
		<h2 class="form-signin-heading">Login</h2>
		<br/>
		<?php
			if(!empty($error)) {
				foreach($error as $key=>$value){
					echo '<li><span class="errorText" style="color:red;">' . $value.'</span><br/>';
				}
				echo '<br/>';
			}
		?>
		<input type="text" id="username" name="username" class="form-control" placeholder="Username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : '';?>">
		<br/>
		<input type="password" id="passwd" name="passwd" class="form-control" placeholder="Password">
		<br/>
		<input type="submit" class="btn btn-lg btn-primary" name="submitLogin" value="Sign in" id="submitLogin" style="align:center;">
		&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="register.php" class="btn btn-lg btn-primary">Register</a>
	</form>
</body>
</html>



