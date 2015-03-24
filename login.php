<?php
	$error = array();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	session_start();
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	if(isset($username)){
		$bind = array(
			':username' => $username
		);
		$result = $db->select('user', 'username = :username', $bind);
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
	   		$results = $db->select('user','username = :username AND password = :password',$bind);
	   		if(empty($results)) {
	   			$error[] = "Invalid username or password.";
	   		} else {
	   			$_SESSION["username"] = $username;
	   			header("Location: index.php");
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



