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
    }

?>

<html>
<head>
<title>PHP MySQL Stored Procedure Demo 1</title>
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php dirname(__FILE__);?>/css/bootstrap.css">
<link rel="stylesheet" href="css/table.css" type="text/css" />
<script src="/js/jquery-1.11.2.js"></script>
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top"> <!-- navbar-inverse -->
        <div class="container">
            <ul class="nav navbar-nav navbar-center" style="text-align:center;" >
                <li><a href="employee.php">Employee</a></li>
                <li><a href="department.php">Departments</a></li>
                <li><a href="job_title.php">Job Titles</a></li>
                <li class="active"><a href="view_employees.php">View Employee List</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right" style="text-align:right;" >
                <li><a href="profile.php">Profile</a></li>
                <li style="text-align:right;"><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <?php
        $q = $db->runProcedure('CALL GetEmployeeList()');
        $flag = false;
    ?>
    <div style="padding-top:40px;padding-left:20px;">
        <span class="employee-details" style="font-size: 30px;"> Employee List</span><br/><br/>
        <table>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Current Manager Name</th>
                <th>Current Salary</th>
                <th>Current Department</th>
                <th>Current Department Manager</th>
                <th>Current Title</th>
                <th>Hire Date</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Last Title</th>
                <th>Last Salary</th>
                <th>Last Title From Date</th>
                <th>Last Title To Date</th>
                <th>Last Department Name</th>
                <th>Salary Hike (%)</th>
            </tr>
        <?php while ($r = $q->fetch()): ?>
            <tr>
                <td class="col-xs-2"><?php echo $r['emp_id']; ?></td>
                <td class="col-xs-4"><?php echo $r['emp_name']; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['current_manager_name']) ? $r['current_manager_name'] : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['current_salary']) ? number_format($r['current_salary'],2) : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['current_department']) ? $r['current_department'] : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['current_department_manager']) ? $r['current_department_manager'] : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['current_title']) ? $r['current_title'] : ''; ?></td>

                <td class="col-xs-3"><?php echo !empty($r['hire_date']) ? date('d M Y',strtotime($r['hire_date'])) : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['gender']) ? $r['gender'] : ''; ?></td>
                <td class="col-xs-3"><?php echo !empty($r['date_of_birth']) ? date('d M Y',strtotime($r['date_of_birth'])) : ''; ?></td>

                <td class="col-xs-2"><?php echo !empty($r['last_title']) ? $r['last_title'] : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['last_salary']) ? number_format($r['last_salary'],2) : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['last_title_from_date']) ? date('d M Y',strtotime($r['last_title_from_date'])) : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['last_title_to_date']) ? date('d M Y',strtotime($r['last_title_to_date'])) : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['last_department']) ? $r['last_department'] : ''; ?></td>
                <td class="col-xs-2"><?php echo !empty($r['salary_hike_in_percentage']) ? number_format($r['salary_hike_in_percentage'],2) . '%' : ''; ?></td>
            </tr>

        <?php $flag = true; endwhile; ?>
        </table><br/>

        <?php if($flag): ?>
            <a class="btn btn-primary" href="download_excel.php"> Download as Excel</a>
        <?php endif;?>
    </div>
    </div>
</body>
</html>
