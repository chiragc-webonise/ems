<?php

	require (dirname (__FILE__) . "/php-excel/class-excel-xml.inc.php");
	session_start();
    $config = dirname(__FILE__)."/config/config.php";
    include $config;

    $db = new db("mysql:host=127.0.0.1;port=3306;dbname=ems", "root", "root");
	$q = $db->runProcedure('CALL GetEmployeeList()');

	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');

	define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

	/** Include PHPExcel */
	require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
								 ->setLastModifiedBy("Maarten Balliauw")
								 ->setTitle("PHPExcel Test Document")
								 ->setSubject("PHPExcel Test Document")
								 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
								 ->setKeywords("office PHPExcel php")
								 ->setCategory("Test result file");

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'Employee ID')
				->setCellValue('B1', 'Employee Name')
	            ->setCellValue('C1', 'Current Manager Name')
	            ->setCellValue('D1', 'Current Salary (INR)')
				->setCellValue('E1', 'Current Department')
	            ->setCellValue('F1', 'Current Department Manager')
	            ->setCellValue('G1', 'Current Title')
				->setCellValue('H1', 'Hire Date')
	            ->setCellValue('I1', 'Gender')
	            ->setCellValue('J1', 'DOB')
				->setCellValue('K1', 'Last Title')
	            ->setCellValue('L1', 'Last Salary (INR)')
	            ->setCellValue('M1', 'Last Title From Date')
	            ->setCellValue('N1', 'Last Title To Date')
	            ->setCellValue('O1', 'Last Department Name')
	            ->setCellValue('P1', 'Salary Hike (%)');
 	

	$key = 3;
	while ($r = $q->fetch()) {
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$key, $r['emp_id'])
	            	->setCellValue('B'.$key, $r['emp_name'])
		            ->setCellValue('C'.$key, !empty($r['current_manager_name']) ? $r['current_manager_name'] : '')
		            ->setCellValue('D'.$key, !empty($r['current_salary']) ? number_format($r['current_salary'],2) : '')
					->setCellValue('E'.$key, !empty($r['current_department']) ? $r['current_department'] : '')
		            ->setCellValue('F'.$key, !empty($r['current_department_manager']) ? $r['current_department_manager'] : '')
		            ->setCellValue('G'.$key, !empty($r['current_title']) ? $r['current_title'] : '')
					->setCellValue('H'.$key, !empty($r['hire_date']) ? date('d M Y',strtotime($r['hire_date'])) : '')
		            ->setCellValue('I'.$key, !empty($r['gender']) ? $r['gender'] : '')
		            ->setCellValue('J'.$key, !empty($r['date_of_birth']) ? date('d M Y',strtotime($r['date_of_birth'])) : '')
					->setCellValue('K'.$key, !empty($r['last_title']) ? $r['last_title'] : '')
		            ->setCellValue('L'.$key, !empty($r['last_salary']) ? number_format($r['last_salary'],2) : '')
		            ->setCellValue('M'.$key, !empty($r['last_title_from_date']) ? date('d M Y',strtotime($r['last_title_from_date'])) : '')
		            ->setCellValue('N'.$key, !empty($r['last_title_to_date']) ? date('d M Y',strtotime($r['last_title_to_date'])) : '')
		            ->setCellValue('O'.$key, !empty($r['last_department']) ? $r['last_department'] : '')
		            ->setCellValue('P'.$key, !empty($r['salary_hike_in_percentage']) ? number_format($r['salary_hike_in_percentage'],2) . '%' : '');

		$key ++;
	}

	$objPHPExcel->getActiveSheet()->setTitle('Employee List');

	$objPHPExcel->setActiveSheetIndex(0);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="Employee_list.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>