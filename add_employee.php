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
		$bind = array(':username' => $username);
		$loginDetails = $db->select('users', 'username = :username', $bind);
		if(!$loginDetails)
			header("Location: login.php");


		if(isset($_POST['addEmployee']))
		{
			$name = trim($_POST['name']);
			$date_of_birth = trim($_POST['date_of_birth']);
			$gender = $_POST['gender'];
			$empDetail = $_POST['isManagerEmployee'];
			$selectedManager = '';
			if($empDetail == 'employee')
				$selectedManager = isset($_POST['manager']) ? $_POST['manager'] : -1;
			$hire_date = trim($_POST['hire_date']);

			$selectedDepartment = isset($_POST['department']) ? $_POST['department'] : '';
			$from_dpt_date = trim(isset($_POST['from_dpt_date']) ? $_POST['from_dpt_date'] : '' );
			$to_dpt_date = trim(isset($_POST['to_dpt_date']) ? $_POST['to_dpt_date'] : '' );

			$selectedJobTitle = isset($_POST['jobTitle']) ? $_POST['jobTitle'] : '';
			$from_jobtitle_date = trim(isset($_POST['from_jobtitle_date']) ? $_POST['from_jobtitle_date'] : '' );
			$to_jobtitle_date = trim(isset($_POST['to_jobtitle_date']) ? $_POST['to_jobtitle_date'] : '' );

			$salary = trim($_POST['salary']);
			$from_slry_date = trim($_POST['from_slry_date']);
			$to_slry_date = trim($_POST['to_slry_date']);

			$validate_name = "/^[A-Za-z ]{3,128}$/";
			$validate_salary = "/^[0-9]{4,15}$/";

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

		   	//Validating Department
		   	if(isset($_POST['department']) && $selectedDepartment == -1) {

		   		$error[] = "Department cannot be blank.";
		   	}
		   	if(isset($_POST['from_dpt_date']) && empty($from_dpt_date)) {
		   		$error[] = "Joining department date cannot be blank.";
		   	} else if(!empty($from_dpt_date)){
		   		$dDate = explode('/', $from_dpt_date);
		   		$month = strstr($dDate[0], 'm');
		   		$day = strstr($dDate[1], 'd');
		   		$year = strstr($dDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect  department joining date.";
			   		$_POST['from_dpt_date'] = '';
			   	}
		   	}
		   	if(isset($_POST['from_dpt_date']) && !empty($to_dpt_date)) {
		   		$ldprtDate = explode('/', $to_dpt_date);
		   		$month = strstr($ldprtDate[0], 'm');
		   		$day = strstr($ldprtDate[1], 'd');
		   		$year = strstr($ldprtDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect department leaving date.";
			   		$_POST['to_dpt_date'] = '';
			   	}
		   	}

		   	//Validating Job Title
		   	if(isset($_POST['jobTitle']) && $selectedJobTitle == -1) {
		   		$error[] = "Job title cannot be blank.";
		   	}
		   	if(isset($_POST['from_jobtitle_date']) && empty($from_jobtitle_date)) {
		   		$error[] = "Joining job title date cannot be blank.";
		   	} else if(!empty($from_jobtitle_date)){
		   		$frmJobTitleDate = explode('/', $from_jobtitle_date);
		   		$month = strstr($frmJobTitleDate[0], 'm');
		   		$day = strstr($frmJobTitleDate[1], 'd');
		   		$year = strstr($frmJobTitleDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect  job title joining date.";
			   		$_POST['from_jobtitle_date'] = '';
			   	}
		   	}
		   	if(isset($_POST['to_jobtitle_date']) && !empty($to_jobtitle_date)) {
		   		$toJobTitleDate = explode('/', $to_jobtitle_date);
		   		$month = strstr($toJobTitleDate[0], 'm');
		   		$day = strstr($toJobTitleDate[1], 'd');
		   		$year = strstr($toJobTitleDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect job title leaving date.";
			   		$_POST['to_jobtitle_date'] = '';
			   	}
		   	}

		   	//Validate Salary
		   	if(empty($salary)) {
		   		$error[] = "Salary cannot be blank.";
		   	} else if(!preg_match($validate_salary, $salary)) {
		   		$_POST['salary'] = '';
		   		$error[] = "Invalid salary.";
		   	}
		   	if(empty($from_slry_date)) {
		   		$error[] = "Salary from date cannot be blank.";
		   	} else {
		   		$frmSlryDate = explode('/', $from_slry_date);
		   		$month = strstr($frmSlryDate[0], 'm');
		   		$day = strstr($frmSlryDate[1], 'd');
		   		$year = strstr($frmSlryDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect  salary joining date.";
			   		$_POST['from_slry_date'] = '';
			   	}
		   	}
		   	if(!empty($to_slry_date)) {
		   		$toSlryDate = explode('/', $to_slry_date);
		   		$month = strstr($toSlryDate[0], 'm');
		   		$day = strstr($toSlryDate[1], 'd');
		   		$year = strstr($toSlryDate[2], 'y');
		   		if(!empty($day) || !empty($month) || !empty($year)) {
			   		$error[] = "Incorrect salary leaving date.";
			   		$_POST['to_slry_date'] = '';
			   	}
		   	}

		   	if(empty($error)){
		   		// Insert into Employee Table
		   		$params = array(
		   			'name' => $name,
		   			'gender' => $gender,
		   			'created' => date('Y-m-d H:i:s')
	   			);
		   		if(!empty($date_of_birth)){
		   			$date_of_birth = date('Y-m-d',strtotime($date_of_birth));
		   			$params['date_of_birth'] = $date_of_birth;
		   		}
		   		if(!empty($hire_date)){
		   			$hire_date = date('Y-m-d',strtotime($hire_date));
		   			$params['hire_date'] = $hire_date;
		   		}
	   			if($selectedManager != -1)
	   				$params['manager_id'] = $selectedManager;
	   			$db->insert('employees', $params);
				
		   		// Insert into Users Table to create login for employee
		   		$addEmpCond = 'Order By id DESC limit 1';
				$bindEmp = array();
		   		$empId = $db->select('employees', '', $bindEmp, 'id', $addEmpCond);
		   		if(!empty($empId)){		   			
		   			$bindRole = array(':name' => $empDetail);
		   			$role = $db->select('user_roles','name = :name', $bindRole, 'id');
		   			$userRole = $role[0]['id'];
		   			$firstName = explode(' ',$name);
		   			$username = $firstName[0];
			   		$paramsUser = array(
			   			'name' => $name,
			   			'username' => strtolower($username) . "_" . $empId[0]['id'],
			   			'password' => md5(strtolower($username)),
				   		'role_id' => $userRole,
				   		'created' => date('Y-m-d H:i:s')
		   			);
		   			$db->insert('users',$paramsUser);

		   			//update user_id into emp table
			   		$addUserCond = 'Order By id DESC limit 1';
					$bindEmp = array();
			   		$userId = $db->select('users', '', $bindEmp, 'id', $addUserCond);
			   		if(!empty($userId)) {
			   			$updateBind = array('id' => $empId[0]['id']);
			   			$update = array(
			   				'user_id' => $userId[0]['id'],
			   				'modified' => date('Y-m-d H:i:s')
		   				);
				   		$db->update('employees', $update, 'id = :id', $updateBind);
			   		}

			   		//Enter salary details
			   		if(!empty($salary) && !empty($from_slry_date)) {
			   			$from_slry_date = date('Y-m-d',strtotime($from_slry_date));
			   			$paramSalary = array(
			   				'salary' => $salary,
			   				'employee_id' => $empId[0]['id'],
		   					'from_date' => $from_slry_date,
				   			'created' => date('Y-m-d H:i:s')
		   				);
				   		if(!empty($to_slry_date)){
				   			$to_slry_date = date('Y-m-d',strtotime($to_slry_date));
				   			$paramSalary['to_date'] = $to_slry_date;
				   		}
						$db->insert('salaries', $paramSalary);
			   		}

			   		//Enter employee job title details
			   		if(isset($_POST['jobTitle']) && $selectedJobTitle != -1 && isset($_POST['from_jobtitle_date']) && !empty($from_jobtitle_date)) {
			   			$from_jobtitle_date = date('Y-m-d',strtotime($from_jobtitle_date));
			   			$paramEmpJobTitle = array(
			   				'job_title_id' => $selectedJobTitle,
			   				'employee_id' => $empId[0]['id'],
			   				'from_date' => $from_jobtitle_date,
				   			'created' => date('Y-m-d H:i:s')
		   				);
				   		if(!empty($to_jobtitle_date)){
				   			$to_jobtitle_date = date('Y-m-d',strtotime($to_jobtitle_date));
				   			$paramEmpJobTitle['to_date'] = $to_jobtitle_date;
				   		}
						$db->insert('employees_titles', $paramEmpJobTitle);
			   		}

			   		//Enter details into EmployeeDepartment or ManagerDepartment table
			   		if(isset($_POST['department']) && $selectedDepartment != -1 && $empDetail == 'employee') {
				   		if(!empty($from_dpt_date))
				   			$from_dpt_date = date('Y-m-d',strtotime($from_dpt_date));
			   			$paramsEmpDptmnt = array(
			   				'employee_id' => $empId[0]['id'],
			   				'department_id' => $selectedDepartment,
			   				'from_date' => $from_dpt_date,
			   				'created' => date('Y-m-d H:i:s')
		   				);
				   		if(!empty($to_dpt_date)){
				   			$to_dpt_date = date('Y-m-d',strtotime($to_dpt_date));
				   			$paramsEmpDptmnt['to_date'] = $to_dpt_date;
				   		}
						$db->insert('departments_employees', $paramsEmpDptmnt);
			   		} else if(isset($_POST['department']) && $selectedDepartment != -1) {
				   		if(!empty($from_dpt_date))
				   			$from_dpt_date = date('Y-m-d',strtotime($from_dpt_date));
			   			$paramsMngrDptmnt = array(
			   				'manager_id' => $empId[0]['id'],
			   				'department_id' => $selectedDepartment,
			   				'from_date' => $from_dpt_date,
			   				'created' => date('Y-m-d H:i:s')
		   				);
				   		if(!empty($to_dpt_date)){
				   			$to_dpt_date = date('Y-m-d',strtotime($to_dpt_date));
				   			$paramsMngrDptmnt['to_date'] = $to_dpt_date;
				   		}
						$db->insert('departments_managers', $paramsMngrDptmnt);
			   		}
			   		header("Location: employee.php");
		   		}
			
		   	}
		}
	}
?>
<html>
<head>
<title>Employee Management System - Employee</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
<!-- <link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/jquery-ui.css">
<script src="/js/jquery-ui.js"></script> -->
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

		$("#from_dpt_date").inputmask("mm/dd/yyyy");
		$("#to_dpt_date").inputmask("mm/dd/yyyy");

		$("#from_jobtitle_date").inputmask("mm/dd/yyyy");
		$("#to_jobtitle_date").inputmask("mm/dd/yyyy");

		$("#from_slry_date").inputmask("mm/dd/yyyy");
		$("#to_slry_date").inputmask("mm/dd/yyyy");

		$('input:radio[name=isManagerEmployee]').click(function() {
		  	var val = $('input:radio[name=isManagerEmployee]:checked').val();
		  	if(val == 'employee') {
		  		$('#managerList').show();
		  	} else if(val == 'manager') {
		  		$('#managerList').hide();
		  	}
		});
		<?php if(isset($_POST['isManagerEmployee']) && $_POST['isManagerEmployee'] == 'employee') : ?>
			$("input[name='isManagerEmployee'][value='employee']").attr("checked", true);
			$('#managerList').show();
			<?php if(isset($_POST['manager']) && $_POST['manager'] != -1) : ?>
				$('#manager').val(<?php echo $_POST['manager'];?>);
				
			<?php endif;
		endif; ?>

		<?php if(isset($_POST['department']) && $_POST['department'] != -1) : ?>
			$('#department').val(<?php echo $_POST['department'];?>);
		<?php endif; ?>

		<?php if(isset($_POST['jobTitle']) && $_POST['jobTitle'] != -1) : ?>
			$('#jobTitle').val(<?php echo $_POST['jobTitle'];?>);
		<?php endif; ?>
	});
</script>
<body>

    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
		<div class="container">
			<ul class="nav navbar-nav navbar-center" style="text-align:center;" >
				<li class="active"><a href="employee.php">Employee</a></li>
				<li><a href="department.php">Departments</a></li>
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
			&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><a href="employee.php" class="btn btn-default">Back</a>
		<?php else:?>
			<form name="registerForm" class="form-signin" method="post" enctype="multipart/form-data">
				<h2 class="form-signin-heading">Add Employee</h2>
				<br/>
				<?php
					if(!empty($error)) {
						foreach($error as $key=>$value){
							echo '<li><span class="errorText" style="color:red;">' . $value.'</span><br/>';
						}
						echo '<br/>';
					}
				?>
				<label >Employee Details</label><br/><br/>
				<div>
					<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '';?>">
					<br/>
				</div>
				<div>
					<label class="radio-inline">
						<input type="radio" name="gender" id="isMale" value="male" checked="true"> Male
					</label>
					<label class="radio-inline">
						<input type="radio" name="gender" id="isFemale" value="female"> Female
					</label><br/><br/>
				</div>
				<div>
					<input type="text" name="date_of_birth" id="date_of_birth" class="form-control" placeholder="Date Of Birth" value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';?>">
					<br/>
				</div>
				<div>
					<input type="text" name="hire_date" id="hire_date" class="form-control" placeholder="Date Of Hiring" value="<?php echo isset($_POST['hire_date']) ? $_POST['hire_date'] : '';?>">
					<br/>
				</div>
				<div>
					<label class="radio-inline">
						<input type="radio" name="isManagerEmployee" id="isManager" value="manager" checked="true"> Is Manager
					</label>
					<label class="radio-inline">
						<input type="radio" name="isManagerEmployee" id="isEmployee" value="employee"> Is Employee
					</label><br/><br/>
					<?php 
						$bindRole = array(':name' => 'manager');
			   			$role = $db->select('user_roles','name = :name', $bindRole, 'id');
			   			$managerRoleId = $role[0]['id'];
						$bindUserId = array(':id' => $managerRoleId);
						$userIdList = $db->select('users', 'role_id = :id', $bindUserId, 'id');
						$idList = '';
						if(!empty($userIdList)) {
							foreach($userIdList as $key => $value) {
								$idList[] = $value['id'];
							}
							$idList = implode(',', $idList);
						}
						$where = 'id IN ('. $idList . ')';
						$managerList = $db->select('employees', $where, '', 'id,name');
						if(!empty($managerList)) :
					?>
						<div name="managerList" id="managerList">
							<select class="form-control" name="manager" id="manager" >
								<option value="-1"> Select Manager</option>
								<?php foreach($managerList as $key=>$value){
									echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
								}?>
							</select><br/><br/>
						</div>
					<?php endif; ?>
				</div>
				<?php 
					$departmentList = $db->select('departments', '', '', 'id,name');
					if(!empty($departmentList)) :
				?>
						<hr><label>Department Details</label><br/><br/>
						<div name="dprtmntList" id="dprtmntList">
							<select class="form-control" name="department" id="department" >
								<option value="-1"> Select Department</option>
								<?php foreach($departmentList as $key=>$value){
									echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
								}?>
							</select><br/>
						</div>
					<div>
						<input type="text" name="from_dpt_date" id="from_dpt_date" class="form-control" placeholder="Joining Department Date" value="<?php echo isset($_POST['from_dpt_date']) ? $_POST['from_dpt_date'] : '';?>">
						<br/>
					</div>
					<div>
						<input type="text" name="to_dpt_date" id="to_dpt_date" class="form-control" placeholder="Leaving Department Date" value="<?php echo isset($_POST['to_dpt_date']) ? $_POST['to_dpt_date'] : '';?>">
						<br/>
					</div>
				<?php endif; ?>
				<?php 
					$jobTitleList = $db->select('job_titles', '', '', 'id,title');
					if(!empty($jobTitleList)) :
				?>
					<hr><label>Job Title Details</label><br/><br/>
					<div name="jobTitleList" id="jobTitleList">
							<select class="form-control" name="jobTitle" id="jobTitle" >
								<option value="-1"> Select Job Title</option>
								<?php foreach($jobTitleList as $key=>$value){
									echo '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
								}?>
							</select><br/>
					</div>
					<div>
						<input type="text" name="from_jobtitle_date" id="from_jobtitle_date" class="form-control" placeholder="Joining Job Title Date" value="<?php echo isset($_POST['from_jobtitle_date']) ? $_POST['from_jobtitle_date'] : '';?>">
						<br/>
					</div>
					<div>
						<input type="text" name="to_jobtitle_date" id="to_jobtitle_date" class="form-control" placeholder="Leaving Job Title Date" value="<?php echo isset($_POST['to_jobtitle_date']) ? $_POST['to_jobtitle_date'] : '';?>">
						<br/>
					</div>
				<?php endif; ?>
				<hr><label>Salary Details</label><br/><br/>
				<div>
					<input type="text" name="salary" id="salary" class="form-control" placeholder="Salary" value="<?php echo isset($_POST['salary']) ? $_POST['salary'] : '';?>">
					<br/>
				</div>
				<div>
					<input type="text" name="from_slry_date" id="from_slry_date" class="form-control" placeholder="Salary From Date" value="<?php echo isset($_POST['from_slry_date']) ? $_POST['from_slry_date'] : '';?>">
					<br/>
				</div>
				<div>
					<input type="text" name="to_slry_date" id="to_slry_date" class="form-control" placeholder="Salary To Date" value="<?php echo isset($_POST['to_slry_date']) ? $_POST['to_slry_date'] : '';?>">
					<br/>
				</div>
				<div>
					<input type="submit" class="btn btn-lg btn-primary" value="Add" name="addEmployee" id="addEmployee" style="align:center;">
					&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="department.php" class="btn btn-lg btn-default">Back</a>
				</div>
			</form>
		<?php endif;?>
	</div>
	
	
</body>
</html>



