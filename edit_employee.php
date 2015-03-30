<?php
	session_start();
	$error = array();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : '0';
	$loginDetails = array();
	$id = isset($_GET['id']) ? $_GET['id'] : '';
	if(isset($username)){
		$bindUser = array(
			':username' => $username
		);
		$loginDetails = $db->select('users', 'username = :username', $bindUser);
		if(!$loginDetails)
			header("Location: login.php");

		$roleList = $db->select('user_roles','','','id');
		$adminRoleId = $roleList[0]['id'];
		$managerRoleId = $roleList[1]['id'];
		$empRoleId = $roleList[2]['id'];

		if(isset($_POST['editEmployee']))
		{
			$name = trim($_POST['name']);
			$date_of_birth = trim($_POST['date_of_birth']);
			$gender = $_POST['gender'];
			$empDetail = $_POST['isManagerEmployee'];
			$selectedManager = '';
			if($empDetail == 'employee')
				$selectedManager = isset($_POST['manager']) ? $_POST['manager'] : -1;
			$hire_date = trim($_POST['hire_date']);

			$salary = trim($_POST['salary']);
			$from_slry_date = trim($_POST['from_slry_date']);
			$to_slry_date = trim($_POST['to_slry_date']);

			$selectedDepartment = isset($_POST['department']) ? $_POST['department'] : '';
			$from_dpt_date = trim(isset($_POST['from_dpt_date']) ? $_POST['from_dpt_date'] : '' );
			$to_dpt_date = trim(isset($_POST['to_dpt_date']) ? $_POST['to_dpt_date'] : '' );

			$selectedJobTitle = isset($_POST['jobTitle']) ? $_POST['jobTitle'] : '';
			$from_jobtitle_date = trim(isset($_POST['from_jobtitle_date']) ? $_POST['from_jobtitle_date'] : '' );
			$to_jobtitle_date = trim(isset($_POST['to_jobtitle_date']) ? $_POST['to_jobtitle_date'] : '' );

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
		   		//Find UserInfo for editable employee
		   		$bindEmp = array(':id' => $id);
		   		$employee = $db->select('employees', 'md5(id) = :id', $bindEmp, 'user_id');
		   		$bindUserID = array(':id' => $employee[0]['user_id']);
				$userInfo = $db->select('users', 'id = :id',$bindUserID);

		   		// Update into Employee Table
		   		$updateBindEmp = array('id' => $id);
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
	   			if($selectedManager != -1 )
	   				$updateEmp['manager_id'] = $selectedManager;
	   			else
	   				$updateEmp['manager_id'] = '';

	   			$db->update('employees', $updateEmp, 'md5(id) = :id', $updateBindEmp);

	   			//Find Employee dateils
				$bindEmpId = array(':empId' => $id);
				$empId = $db->select('employees', 'md5(id) = :empId', $bindEmpId, 'id');

		   		//Update department details
		   		if(isset($_POST['department']) && $selectedDepartment != -1 && isset($_POST['from_dpt_date']) && !empty($from_dpt_date)) {
		   			$from_dpt_date = date('Y-m-d',strtotime($from_dpt_date));
					if(!empty($to_dpt_date))
						$to_dpt_date = date('Y-m-d',strtotime($to_dpt_date));

		   			$addDptCond = 'Order by id DESC limit 1';
	   				if($userInfo[0]['role_id'] == $managerRoleId){
	   					$findDptMngrData = $db->select('departments_managers', 'md5(manager_id) = :empId', $bindEmpId, 'id,manager_id,department_id', $addDptCond);

						if(isset($findDptMngrData) && !empty($findDptMngrData) && $findDptMngrData[0]['department_id'] == $selectedDepartment) { 
							//update old salary dates
							$updateBindDpt = array('empID' => $id, ':id' => $findDptMngrData[0]['id']);
					   		$updateDptMngr = array(
				   				'from_date' => $from_jobtitle_date,
					   			'modified' => date('Y-m-d H:i:s')
				   			);
			   				if(!empty($to_dpt_date))
				   				$updateDptMngr['to_date'] = $to_dpt_date;
				   			$db->update('departments_managers', $updateDptMngr, 'md5(manager_id) = :empID AND id = :id', $updateBindDpt);
						} else {
							$newParamDprtmnt = array(
				   				'department_id' => $selectedDepartment,
				   				'manager_id' => $empId[0]['id'],
				   				'from_date' => $from_dpt_date,
					   			'created' => date('Y-m-d H:i:s')
			   				);
			   				if(!empty($to_dpt_date))
				   				$newParamDprtmnt['to_date'] = $to_dpt_date;
					   		
							$db->insert('departments_managers', $newParamDprtmnt);
						}
		   			} else if($userInfo[0]['role_id'] == $empRoleId){
						$findDptEmpData = $db->select('departments_employees', 'md5(employee_id) = :empId', $bindEmpId, 'id,employee_id,department_id', $addDptCond);

						if(isset($findDptEmpData) && !empty($findDptEmpData) && $findDptEmpData[0]['department_id'] == $selectedDepartment){ 
							//update old salary dates
							$updateBindDptEmp = array('empID' => $id, ':id' => $findDptEmpData[0]['id']);
					   		$updateDptEmp = array(
				   				'from_date' => $from_dpt_date,
					   			'modified' => date('Y-m-d H:i:s')
				   			);
			   				if(!empty($to_dpt_date))
				   				$updateDptEmp['to_date'] = $to_dpt_date;
				   			$db->update('departments_employees', $updateDptEmp, 'md5(employee_id) = :empID AND id = :id', $updateBindDptEmp);
						} else { 
							$newParamDptEmp = array(
				   				'department_id' => $selectedDepartment,
				   				'employee_id' => $empId[0]['id'],
				   				'from_date' => $from_dpt_date,
					   			'created' => date('Y-m-d H:i:s')
			   				);
			   				if(!empty($to_dpt_date))
				   				$newParamDptEmp['to_date'] = $to_dpt_date;
					   		
							$db->insert('departments_employees', $newParamDptEmp);
						}
		   			}
					
		   		}

		   		//Update job title details
		   		if(isset($_POST['jobTitle']) && $selectedJobTitle != -1 && isset($_POST['from_jobtitle_date']) && !empty($from_jobtitle_date)) {
		   			$addJTCond = 'Order by id DESC limit 1';
					$findJTData = $db->select('employees_titles', 'md5(employee_id) = :empId', $bindEmpId, 'id,employee_id,job_title_id', $addJTCond);


					$from_jobtitle_date = date('Y-m-d',strtotime($from_jobtitle_date));
					if(!empty($to_jobtitle_date))
						$to_jobtitle_date = date('Y-m-d',strtotime($to_jobtitle_date));

					if(isset($findJTData) && !empty($findJTData) && $findJTData[0]['job_title_id'] == $selectedJobTitle) { 
						//update old title dates
						$updateBindJt = array('empID' => $id, ':id' => $findJTData[0]['id']);
				   		$updateEmpJT = array(
			   				'from_date' => $from_jobtitle_date,
				   			'modified' => date('Y-m-d H:i:s')
			   			);
		   				if(!empty($to_jobtitle_date))
			   				$updateEmpJT['to_date'] = $to_jobtitle_date;
			   			$db->update('employees_titles', $updateEmpJT, 'md5(employee_id) = :empID AND id = :id', $updateBindJt);
					} else { 
						//insert new title and update old title
						$newParamJobTitle = array(
			   				'job_title_id' => $selectedJobTitle,
			   				'employee_id' => $empId[0]['id'],
			   				'from_date' => $from_jobtitle_date,
				   			'created' => date('Y-m-d H:i:s')
		   				);
		   				if(!empty($to_jobtitle_date))
			   				$newParamJobTitle['to_date'] = $to_jobtitle_date;				   		
						$db->insert('employees_titles', $newParamJobTitle);

						//update old title dates
						if(isset($findJTData) && !empty($findJTData)) {
							$updateBindJt = array('empID' => $id, ':id' => $findJTData[0]['id']);
					   		$updateEmpJT = array(
					   			'modified' => date('Y-m-d H:i:s')
				   			);
			   				$updateEmpJT['to_date'] = $from_jobtitle_date;
				   			$db->update('employees_titles', $updateEmpJT, 'md5(employee_id) = :empID AND id = :id', $updateBindJt);
				   		}
					}
		   		}

	   			//Update salary details
		   		if(!empty($salary) && !empty($from_slry_date)) {
		   			$addSlryCond = 'Order by id DESC limit 1';
					$findSlryData = $db->select('salaries', 'md5(employee_id) = :empId', $bindEmpId, 'id,salary', $addSlryCond);

					$from_slry_date = date('Y-m-d',strtotime($from_slry_date));
					if(!empty($to_slry_date))
			   			$to_slry_date = date('Y-m-d',strtotime($to_slry_date));

			   		if(!empty($findSlryData) && $findSlryData[0]['salary'] != $salary){
			   			//insert new salary
			   			$newParamSalary = array(
			   				'salary' => $salary,
			   				'employee_id' => $empId[0]['id'],
			   				'from_date' => $from_slry_date,
				   			'created' => date('Y-m-d H:i:s')
		   				);
		   				if(!empty($to_slry_date))
			   				$newParamSalary['to_date'] = $to_slry_date;
				   		
						$db->insert('salaries', $newParamSalary);

			   			//update old salary
			   			if(!empty($findSlryData)) {
							$updateBindSlry = array('id' => $id, ':slryId' => $findSlryData[0]['id']);
					   		$updateSlry = array(
					   			'modified' => date('Y-m-d H:i:s')
				   			);
			   				$updateSlry['to_date'] = $from_slry_date;
				   			$db->update('salaries', $updateSlry, 'md5(employee_id) = :id AND id = :slryId', $updateBindSlry);
				   		}

					} else {
						//update old salary dates
						$updateBindSlry = array('id' => $id, ':slryId' => $findSlryData[0]['id']);
				   		$updateSlry = array(
			   				'from_date' => $from_slry_date,
				   			'modified' => date('Y-m-d H:i:s')
			   			);
		   				if(!empty($to_slry_date))
			   				$updateSlry['to_date'] = $to_slry_date;
			   			$db->update('salaries', $updateSlry, 'md5(employee_id) = :id AND id = :slryId', $updateBindSlry);
					}
		   		}

		   		header("Location: employee.php");
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

		$("#from_slry_date").inputmask("mm/dd/yyyy");
		$("#to_slry_date").inputmask("mm/dd/yyyy");

		$("#from_dpt_date").inputmask("mm/dd/yyyy");
		$("#to_dpt_date").inputmask("mm/dd/yyyy");

		$("#from_jobtitle_date").inputmask("mm/dd/yyyy");
		$("#to_jobtitle_date").inputmask("mm/dd/yyyy");

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

		//Show Manager List on page load if employee
		var name = $('input:radio[name=isManagerEmployee]:checked').val();
		if(name == 'employee') {
			$('#managerList').show();
		}
	});
</script>
<body>

    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
		<div class="container">
			<ul class="nav navbar-nav navbar-center" style="text-align:center;" >
				<li class="active"><a href="employee.php">Employee</a></li>
				<li><a href="department.php">Departments</a></li>
				<li><a href="job_title.php">Job Titles</a></li>
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
		<?php
			$id = $_GET['id'];
			if(!empty($id) && $id != ''):
				$bindId = array(':id' => $id);
				$employee = $db->select('employees', 'md5(id) = :id', $bindId);
				if(empty($employee)):
					echo "<span style=\"font-size:20px;font-weight:bold;\">Incorrect employee ID.</span>" .
						"&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><a href=\"employee.php\" class=\"btn btn-default\">Back</a>";
				else:		
					$bindUserID = array(':id' => $employee[0]['id']);
					$userInfo = $db->select('users', 'id = :id',$bindUserID);
		?>
				<form name="registerForm" class="form-signin" method="post" enctype="multipart/form-data">
				<h2 class="form-signin-heading">Edit Employee</h2>
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
					<?php if(isset($_POST['name'])) : ?>
						<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : '';?>">
					<?php else : ?>
						<input type="text" name="name" id="name" class="form-control" placeholder="Name" value="<?php echo isset($employee[0]['name']) ? $employee[0]['name'] : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<label class="radio-inline">
						<input type="radio" name="gender" id="isMale" value="male" <?php echo isset($employee[0]['gender']) ? (($employee[0]['gender'] == 'male') ? 'checked="true"' : '') : 'checked="true"';?>> Male
					</label>
					<label class="radio-inline">
						<input type="radio" name="gender" id="isFemale" value="female" <?php echo ($employee[0]['gender'] == 'female') ? 'checked="true"' : '';?>> Female
					</label><br/><br/>
				</div>
				<div>
					<?php if(isset($_POST['date_of_birth'])) : ?>
						<input type="text" name="date_of_birth" id="date_of_birth" class="form-control" placeholder="Date Of Birth" value="<?php echo isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';?>">
					<?php else : ?>
						<input type="text" name="date_of_birth" id="date_of_birth" class="form-control" placeholder="Date Of Birth" value="<?php echo isset($employee[0]['date_of_birth']) ? date('m/d/Y',strtotime($employee[0]['date_of_birth'])) : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<?php if(isset($_POST['hire_date'])) : ?>
						<input type="text" name="hire_date" id="hire_date" class="form-control" placeholder="Date Of Hiring" value="<?php echo isset($_POST['hire_date']) ? $_POST['hire_date'] : '';?>">
					<?php else : ?>
						<input type="text" name="hire_date" id="hire_date" class="form-control" placeholder="Date Of Hiring" value="<?php echo isset($employee[0]['hire_date']) ? date('m/d/Y',strtotime($employee[0]['hire_date'])) : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<label class="radio-inline">
						<input type="radio" name="isManagerEmployee" id="isManager" value="manager" <?php echo ($userInfo[0]['role_id'] == 2) ? 'checked="true"' : '';?>> Is Manager
					</label>
					<label class="radio-inline">
						<input type="radio" name="isManagerEmployee" id="isEmployee" value="employee" <?php echo ($userInfo[0]['role_id'] == 3) ? 'checked="true"' : '';?>> Is Employee
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
									if(isset($employee[0]["manager_id"]) && $employee[0]["manager_id"] == $value["id"])
										echo '<option value="' . $value["id"] . '" selected>' . $value['name'] . '</option>';
									else
										echo '<option value="' . $value["id"] . '">' . $value['name'] . '</option>';
								}?>
							</select><br/><br/>
						</div>
					<?php endif; ?>
				</div>
				<?php
					$addDptCond = 'Order by id DESC limit 1';
					$bindDptEmpId = array(':empId' => $id);
					if($userInfo[0]['role_id'] == $managerRoleId)
						$dprtmntDetail = $db->select('departments_managers', 'md5(manager_id) = :empId', $bindDptEmpId, '*', $addDptCond);
					else if($userInfo[0]['role_id'] == $empRoleId)
						$dprtmntDetail = $db->select('departments_employees', 'md5(employee_id) = :empId', $bindDptEmpId, '*', $addDptCond);

					$departmentList = $db->select('departments', '', '', 'id,name');
					if(!empty($departmentList)) :
				?>
						<hr><label>Department Details</label><br/><br/>
						<div name="dprtmntList" id="dprtmntList">
							<select class="form-control" name="department" id="department" >
								<option value="-1"> Select Department</option>
								<?php foreach($departmentList as $key=>$value){
									if(isset($_POST['department']) && $_POST['department'] == $value["id"])
										echo '<option value="' . $value['id'] . '" selected>' . $value['name'] . '</option>';
									else if(isset($dprtmntDetail[0]["department_id"]) && $dprtmntDetail[0]["department_id"] == $value["id"])
										echo '<option value="' . $value['id'] . '" selected>' . $value['name'] . '</option>';
									else
										echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
								}?>
							</select><br/>
						</div>
					<div>
						<?php if(isset($_POST['from_dpt_date'])) : ?>
							<input type="text" name="from_dpt_date" id="from_dpt_date" class="form-control" placeholder="Joining Department Date" value="<?php echo isset($_POST['from_dpt_date']) ? $_POST['from_dpt_date'] : '';?>">
						<?php else : ?>
							<input type="text" name="from_dpt_date" id="from_dpt_date" class="form-control" placeholder="Joining Department Date" value="<?php echo isset($dprtmntDetail[0]['from_date']) ? date('m/d/Y',strtotime($dprtmntDetail[0]['from_date'])) : '';?>">
						<?php endif;?>
						<br/>
					</div>
					<div>
						<?php if(isset($_POST['to_dpt_date'])) : ?>
							<input type="text" name="to_dpt_date" id="to_dpt_date" class="form-control" placeholder="Leaving Department Date" value="<?php echo isset($_POST['to_dpt_date']) ? $_POST['to_dpt_date'] : '';?>">
						<?php else : ?>
							<input type="text" name="to_dpt_date" id="to_dpt_date" class="form-control" placeholder="Leaving Department Date" value="<?php echo isset($dprtmntDetail[0]['to_date']) ? date('m/d/Y',strtotime($dprtmntDetail[0]['to_date'])) : '';?>">
						<?php endif;?>
						<br/>
					</div>
				<?php endif; ?>
				<?php
					$addJobTitleCond = 'Order by id DESC limit 1';
					$bindJobTitleEmpId = array(':empId' => $id);
					$jobDetail = $db->select('employees_titles', 'md5(employee_id) = :empId', $bindJobTitleEmpId, '*', $addJobTitleCond);

					$jobTitleList = $db->select('job_titles', '', '', 'id,title');
					if(!empty($jobTitleList)) :
				?>
					<hr><label>Job Title Details</label><br/><br/>
					<div name="jobTitleList" id="jobTitleList">
							<select class="form-control" name="jobTitle" id="jobTitle" >
								<option value="-1"> Select Job Title</option>
								<?php foreach($jobTitleList as $key=>$value){
									if(isset($_POST['jobTitle']) && $_POST['jobTitle'] == $value["id"])
										echo '<option value="' . $value['id'] . '" selected>' . $value['title'] . '</option>';
									else if(isset($jobDetail[0]["job_title_id"]) && $jobDetail[0]["job_title_id"] == $value["id"])
										echo '<option value="' . $value['id'] . '" selected>' . $value['title'] . '</option>';
									else
										echo '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
								}?>
							</select><br/>
					</div>
					<div>
						<?php if(isset($_POST['from_jobtitle_date'])) : ?>
							<input type="text" name="from_jobtitle_date" id="from_jobtitle_date" class="form-control" placeholder="Joining Job Title Date" value="<?php echo isset($_POST['from_jobtitle_date']) ? $_POST['from_jobtitle_date'] : '';?>">
						<?php else : ?>
							<input type="text" name="from_jobtitle_date" id="from_jobtitle_date" class="form-control" placeholder="Joining Job Title Date" value="<?php echo isset($jobDetail[0]['from_date']) ? date('m/d/Y',strtotime($jobDetail[0]['from_date'])) : '';?>">
						<?php endif;?>
						<br/>
					</div>
					<div>
						<?php if(isset($_POST['to_jobtitle_date'])) : ?>
							<input type="text" name="to_jobtitle_date" id="to_jobtitle_date" class="form-control" placeholder="Leaving Job Title Date" value="<?php echo isset($_POST['to_jobtitle_date']) ? $_POST['to_jobtitle_date'] : '';?>">
						<?php else : ?>
							<input type="text" name="to_jobtitle_date" id="to_jobtitle_date" class="form-control" placeholder="Leaving Job Title Date" value="<?php echo isset($jobDetail[0]['to_date']) ? date('m/d/Y',strtotime($jobDetail[0]['to_date'])) : '';?>">
						<?php endif;?>
						<br/>
					</div>
				<?php endif; ?>
				<hr><label>Salary Details</label><br/><br/>
				<?php 
					$addSlryCond = 'Order by id DESC limit 1';
					$bindSlryEmpId = array(':empId' => $id);
					$salaryDetail = $db->select('salaries', 'md5(employee_id) = :empId', $bindSlryEmpId, '*', $addSlryCond);
				?>
				<div>
					<?php if(isset($_POST['salary'])) : ?>
						<input type="text" name="salary" id="salary" class="form-control" placeholder="Salary" value="<?php echo isset($_POST['salary']) ? $_POST['salary'] : '';?>">
					<?php else : ?>
						<input type="text" name="salary" id="salary" class="form-control" placeholder="Salary" value="<?php echo !empty($salaryDetail[0]['salary']) ? $salaryDetail[0]['salary'] : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<?php if(isset($_POST['from_slry_date'])) : ?>
						<input type="text" name="from_slry_date" id="from_slry_date" class="form-control" placeholder="Salary From Date" value="<?php echo isset($_POST['from_slry_date']) ? $_POST['from_slry_date'] : '';?>">
					<?php else : ?>
						<input type="text" name="from_slry_date" id="from_slry_date" class="form-control" placeholder="Salary From Date" value="<?php echo isset($salaryDetail[0]['from_date']) ? date('m/d/Y',strtotime($salaryDetail[0]['from_date'])) : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<?php if(isset($_POST['to_slry_date'])) : ?>
						<input type="text" name="to_slry_date" id="to_slry_date" class="form-control" placeholder="Salary To Date" value="<?php echo isset($_POST['to_slry_date']) ? $_POST['to_slry_date'] : '';?>">
					<?php else : ?>
						<input type="text" name="to_slry_date" id="to_slry_date" class="form-control" placeholder="Salary To Date" value="<?php echo isset($salaryDetail[0]['to_date']) ? date('m/d/Y',strtotime($salaryDetail[0]['to_date'])) : '';?>">
					<?php endif;?>
					<br/>
				</div>
				<div>
					<input type="submit" class="btn btn-lg btn-primary" value="Update" name="editEmployee" id="editEmployee" style="align:center;">
					&nbsp;&nbsp;&nbsp;&nbsp;or&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="department.php" class="btn btn-lg btn-default">Back</a>
				</div>
			</form>
			<?php endif;
			endif; ?>
		<?php endif;?>
	</div>
	
	
</body>
</html>



