<html>
<head>
<title>Employee Management System </title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
		<div class="container">
			<ul class="nav navbar-nav navbar-center" style="text-align:center;" >
				<li><a href="employee.php">Employee</a></li>
				<li><a href="department.php">Departments</a></li>
				<li><a href="job_title.php">Job Titles</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right" style="text-align:right;" >
				<li><a href="profile.php">Profile</a></li>
				<li style="text-align:right;"><a href="logout.php">Logout</a></li>
			</ul>
		</div>
	</nav>

	<?php
		session_start();
		$config = dirname(__FILE__)."/config/config.php";
		include $config;
		$header = dirname(__FILE__)."/config/header.php";
		include $header;
		$db = new db("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
		$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
		if(isset($username)){
			$bind = array(
				':username' => $username
			);
			$result = $db->select('users', 'username = :username', $bind);
			if(!$result)
				header("Location: login.php");

			header("Location: employee.php");
		}
	?>
	
	
</body>
</html>



