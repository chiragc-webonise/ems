-- ----------------------------
-- Database structure for Employee Management System
-- ----------------------------


-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS user;
CREATE TABLE IF NOT EXISTS user(
	id int(8) NOT NULL AUTO_INCREMENT,
	name varchar(128) NOT NULL,
	username varchar(128) NOT NULL,
	password varchar(128) NOT NULL,
	role_id int(8) NOT NULL,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (role_id) REFERENCES user_roles(id) on UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for user_roles
-- ----------------------------
DROP TABLE IF EXISTS user_roles;
CREATE TABLE IF NOT EXISTS user_roles(
	id int(8) NOT NULL AUTO_INCREMENT,
	name varchar(16) NOT NULL,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

------- DEFAULT VALUES

INSERT INTO `ems`.`user_roles` (`id`, `name`, `created`, `modified`)
VALUES ('1', 'admin', '2015-03-24 14:23:43', NULL);
INSERT INTO `ems`.`user_roles` (`id`, `name`, `created`, `modified`)
VALUES ('2', 'manager', '2015-03-24 14:23:49', NULL);
INSERT INTO `ems`.`user_roles` (`id`, `name`, `created`, `modified`)
VALUES ('3', 'employee', '2015-03-24 14:23:57', NULL);



-- ----------------------------
-- Table structure for employees
-- ----------------------------
DROP TABLE IF EXISTS employees;
CREATE TABLE IF NOT EXISTS employees(
	id int(8) NOT NULL AUTO_INCREMENT,
	name varchar(128) NOT NULL,
	manager_id int(8),
	date_of_birth varchar(10) DEFAULT NULL,
	gender varchar(10) DEFAULT NULL,
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
	department_id int(8) NOT NULL,
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees(id) on UPDATE CASCADE,
	FOREIGN KEY (department_id) REFERENCES departments(id) on UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for departments_managers
-- ----------------------------
DROP TABLE IF EXISTS departments_managers;
CREATE TABLE IF NOT EXISTS departments_managers(
	id int(8) NOT NULL AUTO_INCREMENT,
	manager_id int(8) NOT NULL,
	department_id int(8) NOT NULL,
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (manager_id) REFERENCES employees(id) on UPDATE CASCADE,
	FOREIGN KEY (department_id) REFERENCES departments(id) on UPDATE CASCADE
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
	FOREIGN KEY (employee_id) REFERENCES employees(id) on UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for job_titles
-- ----------------------------
DROP TABLE IF EXISTS job_titles;
CREATE TABLE IF NOT EXISTS job_titles(
	id int(8) NOT NULL AUTO_INCREMENT,
	title varchar(128) NOT NULL,
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
	job_title_id int(8) NOT NULL,
	salary float NOT NULL,
	from_date datetime NOT NULL,
	to_date datetime,
	created datetime NOT NULL,
	modified datetime,
	PRIMARY KEY (id),
	FOREIGN KEY (employee_id) REFERENCES employees(id) on UPDATE CASCADE,
	FOREIGN KEY (job_title_id) REFERENCES job_titles(id) on UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;