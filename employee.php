<?php
	session_start();
	$config = dirname(__FILE__)."/config/config.php";
	include $config;
	$db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
	$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : '0';
	$loginEmpID = isset($_SESSION['empID']) ? $_SESSION['empID'] : '';
	$loginDetails = array();
	if(isset($username)){
		$bind = array(
			':username' => $username
		);
		$loginDetails = $db->select('users', 'username = :username', $bind);
		if(!$loginDetails)
			header("Location: login.php");

		$roleList = $db->select('user_roles','','','id');
		$adminRoleId = $roleList[0]['id'];
		$managerRoleId = $roleList[1]['id'];
		$empRoleId = $roleList[2]['id'];
	}
?>
<html>
<head>
<title>Employee Management System - View</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
<script src="/js/jquery-1.11.2.js"></script>
</head>
<script type="text/javascript">
	$(document).ready(function(){
		$('.edit-employee').click(function(){
			var id = $(this).attr('id');
			window.location = 'edit_employee.php?id='+id;
		});
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

	<div style="padding-top:40px;padding-left:50px;" class="employee">
		<?php if(!empty($loginDetails)) { ?>
			<span class="employee-details"> Employee Details</span><br/><br/>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th style="text-align:center;">#</th>
						<th style="text-align:center;">Name</th>
						<th style="text-align:center;">Department</th>
						<th style="text-align:center;">Job Title</th>
						<th style="text-align:center;">Hiring Date</th>
						<th style="text-align:center;">Created On</th>
						<?php if($isAdmin): ?>
							<th style="text-align:center;">Actions</th>
						<?php endif;?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$bindLoginEmp = array(':empID' => $loginEmpID);
						$empDetails = $db->select('employees', 'md5(id) != :empID', $bindLoginEmp, '*', 'order by name ASC');
						if(!empty($empDetails)) :
				    		foreach($empDetails as $key => $value):
				    			$bindId = array(':user_id' => $value['user_id']);
				    			$userDetail = $db->select('users', 'id =:user_id', $bindId, 'role_id');?>
								<tr>
									<td style="text-align:center;"><?php echo ($key+1);?></td>
									<td >
										<?php if($empDetails[$key]['gender'] == 'male') : ?>
											<img style="align:right;"src="/images/male.png"/>
										<?php elseif($empDetails[$key]['gender'] == 'female') : ?>
											<img style="align:right;"src="/images/female.png"/>
										<?php endif;?>
										<?php 
											if($userDetail[0]['role_id'] != $adminRoleId)
												echo $value['name'];
											else
												echo $value['name'] .' &nbsp;&nbsp;<img style="align:right;"src="/images/admin.png"/>';
										?>
									</td>
									<td >
										<?php
											$addCond = 'Order by id DESC limit 1';
											$departmentIdList = array();
											if($userDetail[0]['role_id'] == $empRoleId){
												$bindEmpId = array(':id' => $value['id']);
												$departmentIdList = $db->select('departments_employees', 'employee_id = :id', $bindEmpId, 'department_id', $addCond);
											} else if($userDetail[0]['role_id'] == $managerRoleId){
												$bindEmpId = array(':id' => $value['id']);
												$departmentIdList = $db->select('departments_managers', 'manager_id = :id', $bindEmpId, 'department_id', $addCond);
											}
											if(!empty($departmentIdList)) {
												$bindDepartmentId = array(':id' => $departmentIdList[0]['department_id']);
												$departmentName = $db->select('departments', 'id = :id', $bindDepartmentId, 'name');
												echo $departmentName[0]['name'];
											} else {
												echo "";	
											}
										?>
									</td>
									<td >
										<?php
											$addCond = 'Order by id DESC limit 1';
											$jobTitleIdList = array();
											$bindEmpId = array(':id' => $value['id']);
											$jobTitleIdList = $db->select('employees_titles', 'employee_id = :id', $bindEmpId, 'job_title_id', $addCond);
											if(!empty($jobTitleIdList)) {
												$bindJobTitleId = array(':id' => $jobTitleIdList[0]['job_title_id']);
												$jobTitleName = $db->select('job_titles', 'id = :id', $bindJobTitleId, 'title');
												echo $jobTitleName[0]['title'];
											} else {
												echo "";	
											}
										?>
									</td>
									<td style="text-align:center;"><?php echo !empty($value['hire_date']) ? date('d M Y',strtotime($value['hire_date'])) : '';?></td>
									<td style="text-align:center;"><?php echo date('d M Y',strtotime($value['created']));?></td>
									<?php if($isAdmin): ?>
										<td style="text-align:center;" class="col-md-1"> 
											<a class="edit-employee" id="<?php echo md5($value['id']);?>">
									          <span class="glyphicon glyphicon-pencil"></span> 
									        </a>
										</td>
									<?php endif;?>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
						<tr>
							<td colspan=7> <i> No results found!</i></td>
						</tr>
					<?php endif; ?>	
				</tbody>
			</table>
			<?php if($isAdmin): ?>
				<a href="add_employee.php" style="float:right;" class="btn btn-default">Add Employee</a>
			<?php endif;?>
		<?php }?>	
	</div>

</body>
</html>