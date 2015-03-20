-- ----------------------------
-- Database structure for Employee Management System
-- ----------------------------


-- ----------------------------
-- Table structure for employees
-- ----------------------------
DROP TABLE IF EXISTS employees;
CREATE TABLE IF NOT EXISTS employees(
	id int(8) NOT NULL AUTO_INCREMENT,
	name varchar(128) NOT NULL,
	manager_id int(8),
	date_of_birth varchar(10) NOT NULL,
	gender varchar(10) NOT NULL,
	hire_date datetime NOT NULL,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for departments
-- ----------------------------
DROP TABLE IF EXISTS departments;
CREATE TABLE IF NOT EXISTS departments(
	id int(8) NOT NULL AUTO_INCREMENT,
	name varchar(128) NOT NULL,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for departments_employees
-- ----------------------------
DROP TABLE IF EXISTS departments_employees;
CREATE TABLE IF NOT EXISTS departments_employees(
	id int(8) NOT NULL AUTO_INCREMENT,
	employee_id int(8) NOT NULL,
	department_id int(8),
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees(id),
	FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for departments_managers
-- ----------------------------
DROP TABLE IF EXISTS departments_managers;
CREATE TABLE IF NOT EXISTS departments_managers(
	id int(8) NOT NULL AUTO_INCREMENT,
	manager_id int(8) NOT NULL,
	department_id int(8),
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (manager_id) REFERENCES employees(id),
	FOREIGN KEY (department_id) REFERENCES departments(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for salaries
-- ----------------------------
DROP TABLE IF EXISTS salaries;
CREATE TABLE IF NOT EXISTS salaries(
	id int(8) NOT NULL AUTO_INCREMENT,
	employee_id int(8) NOT NULL,
	salary float NOT NULL,
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for job_titles
-- ----------------------------
DROP TABLE IF EXISTS job_titles;
CREATE TABLE IF NOT EXISTS job_titles(
	id int(8) NOT NULL AUTO_INCREMENT,
	title varchar(32) NOT NULL,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for employees_titles
-- ----------------------------
DROP TABLE IF EXISTS employees_titles;
CREATE TABLE IF NOT EXISTS employees_titles(
	id int(8) NOT NULL AUTO_INCREMENT,
	employee_id int(8) NOT NULL,
	job_title_id varchar(32) NOT NULL,
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees(id),
	FOREIGN KEY (job_title_id) REFERENCES job_titles(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;