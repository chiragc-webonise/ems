<?php


 	require (dirname (__FILE__) . "/php-excel/class-excel-xml.inc.php");
	session_start();
    $config = dirname(__FILE__)."/config/config.php";
    include $config;
    $db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$q = $db->runProcedure('CALL GetEmployeeList()');

	$data = array();
	$data[1][] = 'Employee ID';
    $data[1][] = 'Employee Name';
    $data[1][] = 'Current Manager Name';
    $data[1][] = 'Current Salary';
    $data[1][] = 'Current Department';
    $data[1][] = 'Current Department Manager';
	$data[1][] = 'Current Title';
	$data[1][] = 'Hire Date';
	$data[1][] = 'Gender';
	$data[1][] = 'DOB';
	$data[1][] = 'Last Title';
	$data[1][] = 'Last Salary';
	$data[1][] = 'Last Title From Date';
	$data[1][] = 'Last Title To Date';
	$data[1][] = 'Last Department Name';
	$data[1][] = 'Salary Hike (%)';

	$key = 2;
	while ($r = $q->fetch()) {
		$data[$key][] = $r['emp_id'];
		$data[$key][] = $r['emp_name'];
		$data[$key][] = !empty($r['current_manager_name']) ? $r['current_manager_name'] : '';
		$data[$key][] = !empty($r['current_salary']) ? number_format($r['current_salary'],2) : '';
		$data[$key][] = !empty($r['current_department']) ? $r['current_department'] : '';
		$data[$key][] = !empty($r['current_department_manager']) ? $r['current_department_manager'] : '';
		$data[$key][] = !empty($r['current_title']) ? $r['current_title'] : '';

		$data[$key][] = !empty($r['hire_date']) ? date('d M Y',strtotime($r['hire_date'])) : '';
		$data[$key][] = !empty($r['gender']) ? $r['gender'] : '';
		$data[$key][] = !empty($r['date_of_birth']) ? date('d M Y',strtotime($r['date_of_birth'])) : '';

		$data[$key][] = !empty($r['last_title']) ? $r['last_title'] : '';
		$data[$key][] = !empty($r['last_salary']) ? number_format($r['last_salary'],2) : '';
		$data[$key][] = !empty($r['last_title_from_date']) ? date('d M Y',strtotime($r['last_title_from_date'])) : '';
		$data[$key][] = !empty($r['last_title_to_date']) ? date('d M Y',strtotime($r['last_title_to_date'])) : '';
		$data[$key][] = !empty($r['last_department']) ? $r['last_department'] : '';
		$data[$key][] = !empty($r['salary_hike_in_percentage']) ? number_format($r['salary_hike_in_percentage'],2) . '%' : '';
		$key ++;
	}

	$xls = new Excel_XML;
	$xls->addArray($data);
	$xls->setWorksheetTitle('List');
	$xls->generateXML ("aaaaaaaaa");
?>


                