DELIMITER //
 CREATE PROCEDURE GetEmployeeList()
   BEGIN
		Select 
			emp.id as emp_id,
			emp.name as emp_name,
			man.name as current_manager_name,
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					SELECT sal.salary 
					from salaries as sal
					where sal.employee_id = emp.id
					order by sal.id desc limit 1
				) ELSE NULL END as current_salary,
			CASE WHEN ur.name = 'employee' THEN
				(
					SELECT dpt.name 
					from departments_employees as dpt_emp
					INNER JOIN departments as dpt on dpt.id = dpt_emp.department_id
					where dpt_emp.employee_id = emp.id
					order by dpt_emp.id desc limit 1
				) ELSE NULL END as current_department,
			CASE WHEN ur.name = 'employee' THEN
				(
					Select man_emp.name 
					from departments_managers as dpt_mngr
					INNER JOIN employees as man_emp on man_emp.id = dpt_mngr.manager_id
					where dpt_mngr.department_id  = (SELECT dpt_emp.department_id 
							from departments_employees as dpt_emp
							where dpt_emp.employee_id = emp.id
							order by dpt_emp.id desc limit 1
						)
					order by dpt_mngr.id desc limit 1
				) ELSE NULL END as current_department_manager,
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					SELECT job_title.title 
					from employees_titles as job_title_emp
					INNER JOIN job_titles as job_title on job_title.id = job_title_emp.job_title_id
					where job_title_emp.employee_id = emp.id
					order by job_title_emp.id desc limit 1
				) ELSE NULL END as current_title,
			emp.hire_date as hire_date,
			emp.gender as gender,
		 	emp.date_of_birth as date_of_birth,
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					SELECT job_title.title
					from employees_titles as job_title_emp
					INNER JOIN job_titles as job_title on job_title.id = job_title_emp.job_title_id
					where job_title_emp.employee_id = emp.id
					order by job_title_emp.id desc limit 1,1
				) ELSE NULL END as last_title,
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					SELECT sal.salary 
					from salaries as sal
					where sal.employee_id = emp.id
					order by sal.id desc limit 1,1
				) ELSE NULL END as last_salary,
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					SELECT job_title_emp.from_date
					from employees_titles as job_title_emp
					INNER JOIN job_titles as job_title on job_title.id = job_title_emp.job_title_id
					where job_title_emp.employee_id = emp.id
					order by job_title_emp.id desc limit 1,1
				) ELSE NULL END as last_title_from_date,
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					SELECT job_title_emp.to_date
					from employees_titles as job_title_emp
					INNER JOIN job_titles as job_title on job_title.id = job_title_emp.job_title_id
					where job_title_emp.employee_id = emp.id
					order by job_title_emp.id desc limit 1,1
				) ELSE NULL END as last_title_to_date,
			CASE WHEN ur.name = 'employee' THEN
				(
					SELECT dpt.name 
					from departments_employees as dpt_emp
					INNER JOIN departments as dpt on dpt.id = dpt_emp.department_id
					where dpt_emp.employee_id = emp.id
					order by dpt_emp.id desc limit 1,1
				) ELSE NULL END as last_department,	
			CASE WHEN ur.name = 'employee' || ur.name = 'manager' THEN
				(
					((SELECT sal.salary from salaries as sal where sal.employee_id = emp.id order by sal.id desc limit 1) - 
						(SELECT sal.salary from salaries as sal where sal.employee_id = emp.id order by sal.id desc limit 1,1)
					) / 
					(SELECT sal.salary from salaries as sal where sal.employee_id = emp.id order by sal.id desc limit 1,1) * 100
				) ELSE NULL END as salary_hike_in_percentage

		from employees emp
		LEFT JOIN employees as man on man.id = emp.manager_id
		INNER JOIN users as user on user.id = emp.user_id
		INNER JOIN user_roles as ur on ur.id = user.role_id
		ORDER BY emp.id;
   END //
DELIMITER ;