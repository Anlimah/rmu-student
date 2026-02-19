-- -----------------------------------------------------
-- Table `academic_year`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `academic_year`;
CREATE TABLE IF NOT EXISTS `academic_year` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `active` TINYINT(1) DEFAULT 1,
  `start_month` VARCHAR(5) NOT NULL, 
  `end_month` VARCHAR(5) NOT NULL,
  `start_year` YEAR NOT NULL, 
  `end_year` YEAR NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `name` VARCHAR(15) GENERATED ALWAYS AS (CONCAT(`start_year`, '-', `end_year`)) PERSISTENT,
  PRIMARY KEY (`id`)
);
CREATE INDEX academic_year_active_idx1 ON `academic_year` (`active`);
CREATE INDEX academic_year_start_month_idx1 ON `academic_year` (`start_month`);
CREATE INDEX academic_year_end_month_idx1 ON `academic_year` (`end_month`);
CREATE INDEX academic_year_start_year_idx1 ON `academic_year` (`start_year`);
CREATE INDEX academic_year_end_year_idx1 ON `academic_year` (`end_year`);
CREATE INDEX academic_year_archived_idx1 ON `academic_year` (`archived`);
CREATE INDEX academic_year_name_idx1 ON `academic_year` (`name`);
INSERT INTO `academic_year` (`start_month`, `start_year`, `end_month`, `end_year`) VALUES ('Sep', '2023', 'Jun', '2024');

-- -----------------------------------------------------
-- Table `semester`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `semester`;
CREATE TABLE IF NOT EXISTS `semester` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `active` TINYINT(1) DEFAULT 1,
    `name` INT NOT NULL,
    `course_registration_opened` TINYINT(1) DEFAULT 0,
    `registration_end` DATE,
    `exam_results_uploaded` TINYINT(1) DEFAULT 0,
    `archived` TINYINT(1) DEFAULT 0,
    `fk_academic_year` INT NULL, -- FK
    PRIMARY KEY (`id`),
  CONSTRAINT `fk_semester_academic_year1` FOREIGN KEY (`fk_academic_year`) REFERENCES `academic_year` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX semester_active_idx1 ON `semester` (`active`);
CREATE INDEX semester_name_idx1 ON `semester` (`name`);
CREATE INDEX semester_course_registration_opened_idx1 ON `semester` (`course_registration_opened`);
CREATE INDEX semester_archived_idx1 ON `semester` (`archived`);
INSERT INTO `semester` (`name`, `course_registration_opened`, `fk_academic_year`) VALUES ('SEMESTER 1', 1, 1);

-- -----------------------------------------------------
-- Table `department`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`)
);
CREATE INDEX department_archived_idx1 ON `department` (`archived`);
INSERT INTO `department`(`name`) VALUES ('ICT'), ('MARINE'), ('NAUTICAL'), ('ELECTRICAL'), ('TRANSPORT');

-- -----------------------------------------------------
-- Table `course_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course_category`;
CREATE TABLE IF NOT EXISTS `course_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(25) NOT NULL UNIQUE,
  `archived` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`)
);
CREATE INDEX course_category_archived_idx1 ON `course_category` (`archived`);
INSERT INTO `course_category`(`name`) VALUES ('compulsory'), ('elective');

-- -----------------------------------------------------
-- Table `course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `code` VARCHAR(10) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `credits` INT DEFAULT 0,
  `semester` INT NOT NULL,
  `level` INT NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_category` INT ,
  `fk_department` INT,
  PRIMARY KEY (`code`),
  CONSTRAINT `fk_course_category1` FOREIGN KEY (`fk_category`) REFERENCES `course_category` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
CONSTRAINT `fk_course_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX course_name_idx1 ON `course` (`name`);
CREATE INDEX course_credits_idx1 ON `course` (`credits`);
CREATE INDEX course_semester_idx1 ON `course` (`semester`);
CREATE INDEX course_level_idx1 ON `course` (`level`);
CREATE INDEX course_archived_idx1 ON `course` (`archived`);

INSERT INTO `course`(`code`, `name`, `credits`, `semester`, `level`, `fk_category`, `fk_department`) VALUES 
("BCME 407", "Digital Signal Processing", "3", "1", "400", "1", "1"),
("BEEE 409", "Linear Systems", "3", "1", "400", "1", "1"),
("BCME 405", "Micro Processor Systems & Application", "3", "1", "400", "1", "1"),
("BCME 417", "Web Software Architecture (Elective)", "3", "1", "400", "1", "1"),
("BCME 400", "Project I", "3", "1", "400", "1", "1"),
("BCME 401", "Artificial Intelligence", "3", "1", "400", "1", "1"),
("BCME 413", "E-Business/Commerce", "3", "1", "400", "1", "1"),
("BCME 419", "Wireless & Mobile Computing", "3", "1", "400", "1", "1"),
("BINT 403", "Management Information System", "3", "1", "400", "1", "1"),
("BINT 401", "Mobile Computing", "3", "1", "400", "1", "1"),
("BINT 407", "Wireless Technologies", "3", "1", "400", "1", "1"),
("BINT 402", "E-Business", "3", "2","400", "1", "1"),
("BINT 404", "IT Entrepreneurship", "3", "2","400", "1", "1"),
("BINT 406", "Computer & Information Security", "3", "2","400", "1", "1"),
("BINT 408", "System Administration", "3", "2","400", "1", "1"),
("BINT 410", "Data Warehousing & Mining", "3", "2","400", "1", "1"),
("BCME 418", "System Security & Administration", "3", "2","400", "1", "1"),
("BCME 404", "Embedded Systems", "3", "2","400", "1", "1"),
("BPSA 412", "Entrepreneurship & Small Business Management", "3", "2","400", "1", "1"),
("BCME 414", "Web Software Architecture", "3", "2","400", "1", "1"),
("BCME 422", "Wireless Communication Systems", "3", "2","400", "1", "1"),
("BCME 408", "Advanced Computer Architecture", "3", "2","400", "1", "1"),
("BCME 410", "Intro. To Database And Information Management", "3", "2","400", "1", "1"),
("BEEE 404", "Digital Control Systems", "3", "2","400", "1", "1"),
("BCME 402", "Project II", "3", "2","400", "1", "1"),
("BPSA 301", "Principles Of Management", "3", "1", "400", "1", "1"),
("BCME 303", "Computer Communication Networks", "3", "1", "300", "1", "1"),
("BCME 309", "Design & Analysis Of Digital Systems", "3", "1", "300", "1", "1"),
("BCME 304", "Discrete Mathematics", "3", "1", "300", "1", "1"),
("BCME 311", "Digital Communication", "3", "1", "300", "1", "1"),
("BCME 305", "Operating Systems", "3", "1", "300", "1", "1"),
("BEEE 303", "Microprocessor Systems (Digital Electronics III)", "3", "1", "300", "1", "1"),
("BSMA 301", "Statistics & Probability", "3", "1", "300", "1", "1"),
("BINT 301", "Operating Systems", "3", "1", "300", "1", "1"),
("BINT 303", "Database Systems I", "3", "1", "300", "1", "1"),
("BINT 305", "Data Communication & Computer Networks I", "3", "1", "300", "1", "1"),
("BINT 307", "Research Methods (Moved to 2nd Sem.)", "3", "1", "300", "1", "1"),
("BINT 309", "Business Intelligence System", "3", "1", "300", "1", "1"),
("BINT 311", "Programming With .Net", "3", "1", "300", "1", "1"),
("BCME 301", "Computer Communication Networks", "3", "1", "300", "1", "1"),
("BCME 302", "IT Project Management", "3", "1", "300", "1", "1"),
("BCME 306", "Introduction to Visual Basic", "3", "1", "300", "1", "1"),
("BCME 307", "Computer Architecture", "3", "1", "300", "1", "1"),
("BCME 310", "Operating Systems", "3", "1", "300", "1", "1"),
("BEME 311", "Formal Methods & Models", "3", "1", "300", "1", "1"),
("BCME 315", "Web Technologies", "3", "1", "300", "1", "1"),
("BINT 302", "IT Project Management", "3", "2","300", "1", "1"),
("BINT 304", "Database Systems II", "3", "2","300", "1", "1"),
("BINT 306", "Data Communication & Computer Networks II", "3", "2","300", "1", "1"),
("BINT 308", "Human Computer Interaction", "3", "2","300", "1", "1"),
("BINT 312", "Programming With Python", "3", "2","300", "1", "1"),
("BCME 308", "Data Mining & Warehousing", "3", "2","300", "1", "1"),
("BCME 312", "Management Information Systems", "3", "2","300", "1", "1"),
("BSMA 302", "Numerical Analysis", "3", "2","300", "1", "1"),
("BSMA 314", "Research Methods", "2", "2","300", "1", "1"),
("BEEE 308", "Communication Engineering II", "4", "2","300", "1", "1"),
("DITE 200", "Project", "3", "1", "200", "1", "1"),
("DITE 201", "Systems Analysis & Design", "3", "1", "200", "1", "1"),
("DITE 203", "Object Oriented Programming (Java)", "3", "1", "200", "1", "1"),
("DITE 205", "Data Communication & Computer Networks I", "3", "1", "200", "1", "1"),
("DITE 207", "Operating Systems", "3", "1", "200", "1", "1"),
("DITE 209", "Computer Architecture", "3", "1", "200", "1", "1"),
("DITE 211", "Database Systems I", "3", "1", "200", "1", "1"),
("DITE 215", "Information Security", "3", "1", "200", "1", "1"),
("DITE 299", "Research Methods (Non-Examinable)", "3", "1", "200", "1", "1"),
("BCME 201", "Programming Language I (C++)", "3", "1", "200", "1", "1"),
("BCME 203", "Data Structures & Algorithms", "3", "1", "200", "1", "1"),
("BEEE 203", "Digital Electronics I (Combinational Logic)", "3", "1", "200", "1", "1"),
("BEEE 207", "Electronics II (Electronic Systems)", "3", "1", "200", "1", "1"),
("BMAE 205", "Strength Of Material Science", "3", "1", "200", "1", "1"),
("BMAE 207", "Thermodynamics I", "3", "1", "200", "1", "1"),
("BSMA 201", "Mathematics III (Calculus With Differential Equations)", "3", "1", "200", "1", "1"),
("BCME 213", "Database Management System", "3", "1", "200", "1", "1"),
("BCME 205", "Discrete Mathematics", "3", "1", "200", "1", "1"),
("BCME 207", "Object Oriented Programming With Java", "3", "1", "200", "1", "1"),
("BCME 209", "Digital Circuit Design II", "3", "1", "200", "1", "1"),
("BCME 211", "Data Structures & Algorithms", "3", "1", "200", "1", "1"),
("BACC 205", "Principles of Accounting I", "3", "1", "200", "1", "1"),
("BINT 201", "Systems Analysis & Design", "3", "1", "200", "1", "1"),
("BINT 203", "Object Oriented Programming (Principles)", "3", "1", "200", "1", "1"),
("BINT 207", "Introduction to Organizational Behaviour", "3", "1", "200", "1", "1"),
("BINT 209", "Computer Architecture", "3", "1", "200", "1", "1"),
("BINT 205", "Programming (WITH C++)", "3", "1", "200", "1", "1"),
("DITE 202", "E-Business", "3", "2", "200", "1", "1"),
("DITE 204", "Entrepreneurship", "3", "2", "200", "1", "1"),
("DITE 206", "Data Communication & Computer Networks II", "3", "2", "200", "1", "1"),
("DITE 208", "Systems Administration", "3", "2", "200", "1", "1"),
("DITE 210", "Software Engineering", "3", "2", "200", "1", "1"),
("DITE 212", "Database Systems II", "3", "2", "200", "1", "1"),
("BACC 206", "Principles Of Accounting II", "3", "2", "200", "1", "1"),
("BINT 202", "Software Engineering ", "3", "2", "200", "1", "1"),
("BINT 204", "Statistics & Probability", "3", "2","200", "1", "1"),
("BINT 206", "Data Structures & Algorithms ", "3", "2","200", "1", "1"),
("BINT 208", "Introduction To Java Programming", "3", "2","200", "1", "1"),
("BCME 204", "Software Engineering", "3", "2","200", "1", "1"),
("BCME 206", "System Analysis & Design", "3", "2","200", "1", "1"),
("BCME 208", "Programming with C++", "3", "2","200", "1", "1"),
("BEEE 202", "Digital Electronics II", "3", "2","200", "1", "1"),
("BSMA 202", "Statistics & Probability", "3", "2","200", "1", "1"),
-- Code BSMA 204 doesn't exist in the document: added it myself
("BSMA 204", "Complex Analysis & Partial Differential Equations", "3", "2","200", "1", "1"),
("BEEE 204", "Electromagnetic Fields", "3", "2","200", "1", "1"),
("BCME 210", "Object-Oriented Programming in Java", "4", "2","200", "1", "1"),
("BEEE 208", "Elect. Instrumentation & Measurement", "4", "2","200", "1", "1"),
("BCME 202", "Software Engineering (Java)", "4", "2","200", "1", "1"),
("BNAS 208", "Navigational System I", "4", "2","200", "1", "1"),
("BEEE 101", "Applied Electricity", "3", "1", "100", "1", "1"),
("BMAE 101", "Basic Mechanics", "3", "1", "100", "1", "1"),
("BCME 101", "Computer Studies I (Intro To Computer Applications)", "3", "1", "100", "1", "1"),
("BMAE 103", "Engineering Drawing I", "3", "1", "100", "1", "1"),
("UFRE 103", "French I", "2", "1", "100", "1", "1"),
("BSMA 101", "Mathematics I (Algebra With Analysis)", "3", "1", "100", "1", "1"),
("BMAE 105", "Workshop Technology I", "3", "1", "100", "1", "1"),
("BMAE 107", "Material Science", "3", "1", "100", "1", "1"),
("UCOM 101", "Communication Skills I (Academic Writing Skills)", "2", "1", "100", "1", "1"),
("BCME 105", "Moral & Ethical Issues", "3", "1", "100", "1", "1"),
("UFRE 101", "French I", "2", "1", "100", "1", "1"),
("BPSA 101", "Introduction to Micro Economics", "3", "1", "100", "1", "1"),
("BINT 101", "Introduction To Computing", "3", "1", "100", "1", "1"),
("BINT 103", "Principles of Programming & Problem Solving", "3", "1", "100", "1", "1"),
("BINT 105", "Critical Thinking & Practical Reasoning", "3", "1", "100", "1", "1"),
("SBUS 105", "Principles of Management", "3", "1", "100", "1", "1"),
("UCOM 102", "Communication Skills I", "2", "2", "100", "1", "1"),
("BSMA 102", "Integral Calculus & Linear Algebra", "4", "2", "100", "1", "1"),
("DITE 102", "Leadership & Ethical Issues In IT", "2", "2", "100", "1", "1"),
("DITE 104", "Introduction To Web Design & Internet Technologies", "3", "2", "100", "1", "1"),
("DITE 106", "Programming With C++", "4", "2", "100", "1", "1"),
("DITE 108", "Basic Accounting", "3", "2", "100", "1", "1"),
("DITE 110", "Basic Digital Electronics", "3", "2", "100", "1", "1"),
("UFRE 104", "French II", "2", "2", "100", "1", "1"),
("BINT 102", "Leadership & Ethical Issues In IT", "2", "2", "100", "1", "1"),
("BINT 104", "Intro To Web Design & Internet Technologies", "3", "2", "100", "1", "1"),
("BINT 106", "Basic Digital Electronics", "3", "2", "100", "1", "1"),
("BCME 106", "Digital Circuit Design I", "3", "2", "100", "1", "1"),
("BPSA 104", "Basic Accounting", "2", "2", "100", "1", "1"),
("BPSA 102", "Introduction to Macro Economics", "3", "2", "100", "1", "1"),
("BCME 102", "Programming Language (C++)", "4", "2", "100", "1", "1"),
("BCME 104", "Circuits & Electronics II", "4", "2", "100", "1", "1"),
("BCME 108", "Matlab & Simulink Practice", "3", "2", "100", "1", "1"),
("DITE 101", "Introduction To Computing", "3", "1", "100", "1", "1"),
("DITE 103", "Principles of Programming & Problem Solving", "3", "1", "100", "1", "1"),
("DITE 105", "Critical Thinking & Practical Reasoning", "3", "1", "100", "1", "1");

-- -----------------------------------------------------
-- Table `room`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `room`;
CREATE TABLE IF NOT EXISTS `room` (
  `number` VARCHAR(10) NOT NULL,
  `capacity`INT NOT NULL,
  `location` VARCHAR(255),
  `longitude` VARCHAR(255),
  `latitude` VARCHAR(255),
  `archived` TINYINT(1) DEFAULT 0,
  `fk_department` INT NULL,
  PRIMARY KEY (`number`),
  CONSTRAINT `fk_room_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX room_code_idx1 ON `room` (`capacity`);
CREATE INDEX room_name_idx1 ON `room` (`location`);
CREATE INDEX room_archived_idx1 ON `room` (`archived`);

-- -----------------------------------------------------
-- Table `class`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `class`;
CREATE TABLE IF NOT EXISTS `class` (
  `code` VARCHAR(10) NOT NULL,
  `fk_program` INT NOT NULL,
  PRIMARY KEY (`code`),
  CONSTRAINT `fk_class_program1`FOREIGN KEY (`fk_program`) REFERENCES `programs` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- -----------------------------------------------------
-- Table `student`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
    `index_number` VARCHAR(10) NOT NULL,
    `app_number` VARCHAR(10) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(15) NOT NULL,
    `prefix` VARCHAR(10),
    `first_name` VARCHAR(255) NOT NULL,
    `middle_name` VARCHAR(255),
    `last_name` VARCHAR(255) NOT NULL,
    `suffix` VARCHAR(10),
    `gender` VARCHAR(1) DEFAULT 'F',
    `dob` DATE NOT NULL,
    `nationality` VARCHAR(25) NOT NULL,
    `photo` VARCHAR(255),
    `marital_status` VARCHAR(25),
    `disability` VARCHAR(25),
    `date_admitted` DATE NOT NULL,
    `term_admitted` VARCHAR(15) NOT NULL,
    `stream_admitted` VARCHAR(15) NOT NULL,
    `level_admitted` INT DEFAULT 100, 
    `programme_duration` INT DEFAULT 4,
    `default_password` TINYINT(1) DEFAULT 1,
    `semester_setup` TINYINT(1) DEFAULT 0,
    `archived` TINYINT(1) DEFAULT 0,
    `fk_academic_year` INT, -- FK
    `fk_applicant` INT, -- FK
    `fk_department` INT, -- FK
    `fk_program` INT, -- FK
    `fk_class` VARCHAR(10), -- FK
    PRIMARY KEY (`index_number`),
  CONSTRAINT `fk_student_academic_year1` FOREIGN KEY (`fk_academic_year`) REFERENCES `academic_year` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_applicant1` FOREIGN KEY (`fk_applicant`) REFERENCES `applicants_login` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_program1` FOREIGN KEY (`fk_program`) REFERENCES `programs` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_student_class1` FOREIGN KEY (`fk_class`) REFERENCES `class` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX `student_password_idx1` ON `student` (`password`);
CREATE INDEX `student_phone_number_idx1` ON `student` (`phone_number`);
CREATE INDEX `student_first_name_idx1` ON `student` (`first_name`);
CREATE INDEX `student_last_name_idx1` ON `student` (`last_name`);
CREATE INDEX `student_gender_idx1` ON `student` (`gender`);
CREATE INDEX `student_dob_idx1` ON `student` (`dob`);
CREATE INDEX `student_nationality_idx1` ON `student` (`nationality`);
CREATE INDEX `student_marital_status_idx1` ON `student` (`marital_status`);
CREATE INDEX `student_disability_idx1` ON `student` (`disability`);
CREATE INDEX `student_date_admitted_idx1` ON `student` (`date_admitted`);
CREATE INDEX `student_term_admitted_idx1` ON `student` (`term_admitted`);
CREATE INDEX `student_stream_admitted_idx1` ON `student` (`stream_admitted`);
CREATE INDEX `student_level_admitted_idx1` ON `student` (`level_admitted`);
CREATE INDEX `student_programme_duration_idx1` ON `student` (`programme_duration`);
CREATE INDEX `student_default_password_idx1` ON `student` (`default_password`);
CREATE INDEX `student_semester_setup_idx1` ON `student` (`semester_setup`);
CREATE INDEX `student_archived_idx1` ON `student` (`archived`);

-- -----------------------------------------------------
-- Table `level`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `level`;
CREATE TABLE IF NOT EXISTS `level` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `level` INT NOT NULL,
    `semester` INT NOT NULL,
    `deferred` TINYINT(1) DEFAULT 0,
    `completed` TINYINT(1) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 0,
    `archived` TINYINT(1) DEFAULT 0,
    `fk_student` VARCHAR(10), -- FK
    PRIMARY KEY (`id`),
  CONSTRAINT `fk_course_registration_student1` FOREIGN KEY (`fk_student`) REFERENCES `student` (`index_number`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX level_level_idx1 ON `level` (`level`);
CREATE INDEX level_semester_idx1 ON `level` (`semester`);
CREATE INDEX level_deferred_idx1 ON `level` (`deferred`);
CREATE INDEX level_completed_idx1 ON `level` (`completed`);
CREATE INDEX level_archived_idx1 ON `level` (`archived`);

-- -----------------------------------------------------
-- Table `curriculum`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `curriculum`;
CREATE TABLE IF NOT EXISTS `curriculum` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fk_program` VARCHAR(10) NULL,
  `fk_course` VARCHAR(10) NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_curriculum_program1` FOREIGN KEY (`fk_program`) REFERENCES `program` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_curriculum_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE
);
INSERT INTO `curriculum` (`fk_program`, `fk_course`) VALUES 
-- DIT
-- Level 100 First Semester Courses
(17, 'DITE 101'), (17, 'DITE 103'), (17, 'DITE 105'), (17, 'UCOM 101'), (17, 'UFRE 103'), (17, 'BSMA 101'), (17, 'SBUS 105'),
-- Level 100 Second Semester Courses
(17, 'UCOM 102'), (17, 'BSMA 102'), (17, 'DITE 102'), (17, 'DITE 104'), (17, 'DITE 106'), (17, 'DITE 108'), (17, 'DITE 110'),
-- Level 200 First Semester Courses
(17, 'DITE 201'), (17, 'DITE 203'), (17, 'DITE 205'), (17, 'DITE 207'), (17, 'DITE 209'), (17, 'DITE 211'), (17, 'DITE 215'), (17, 'DITE 200'),
-- Level 200 Second Semester Courses
(17, 'DITE 202'), (17, 'DITE 204'), (17, 'DITE 206'), (17, 'DITE 208'), (17, 'DITE 210'), (17, 'DITE 212'), (17, 'DITE 200'),

-- BIT
-- Level 100 First Semester Courses
(12, 'BINT 101'), (12, 'BINT 103'), (12, 'BINT 105'), (12, 'UCOM 101'), (12, 'UFRE 103'), (12, 'BSMA 101'), (12, 'SBUS 105'),
-- Level 100 Second Semester Courses
(12, 'BSMA 102'), (12, 'UCOM 102'), (12, 'UFRE 104'), (12, 'BINT 102'), (12, 'BINT 104'), (12, 'BINT 106'), (12, 'BINT 205'),
-- Level 200 First Semester Courses
(12, 'BACC 205'), (12, 'BINT 201'), (12, 'BINT 203'), (12, 'BINT 207'), (12, 'BINT 209'), (12, 'BINT 205'), 
-- Level 200 Second Semester Courses
(12, 'BACC 206'), (12, 'BINT 202'), (12, 'BINT 204'), (12, 'BINT 206'), (12, 'BINT 208'), 
-- Level 300 First Semester Courses
(12, 'BINT 301'), (12, 'BINT 303'), (12, 'BINT 305'), (12, 'BINT 307'), (12, 'BINT 309'), (12, 'BINT 311'),
-- Level 300 Second Semester Courses
(12, 'BINT 302'), (12, 'BINT 304'), (12, 'BINT 306'), (12, 'BINT 307'), (12, 'BINT 308'), (12, 'BINT 312'),
-- Level 400 First Semester Courses
(12, 'BINT 401'), (12, 'BINT 403'), (12, 'BCME 401'), (12, 'BINT 407'), (12, 'BCME 400'),
-- Level 400 Second Semester Courses
(12, 'BINT 402'), (12, 'BINT 404'), (12, 'BINT 406'), (12, 'BINT 408'), (12, 'BINT 410'),

-- BCS
-- Level 100 First Semester Courses
(9, 'BEEE 101'), (9, 'UCOM 101'), (9, 'BCME 101'), (9, 'BCME 105'), (9, 'UFRE 101'), (9, 'BSMA 101'), (9, 'BPSA 101'),
-- Level 100 Second Semester Courses
(9, 'UCOM 102'), (9, 'BSMA 102'), (9, 'BINT 104'), (9, 'BCME 106'), (9, 'UFRE 104'), (9, 'BPSA 104'), (9, 'BPSA 102'),
-- Level 200 First Semester Courses
(9, 'BCME 213'), (9, 'BCME 205'), (9, 'BCME 207'), (9, 'BCME 209'), (9, 'BCME 211'), (9, 'BSMA 201'),
-- Level 200 Second Semester Courses
(9, 'BCME 204'), (9, 'BCME 206'), (9, 'BCME 208'), (9, 'BEEE 202'), (9, 'BSMA 202'), (9, 'BSMA 204'),
-- Level 300 First Semester Courses
(9, 'BCME 301'), (9, 'BCME 303'), (9, 'BCME 305'), (9, 'BCME 307'), (9, 'BCME 309'), (9, 'BEME 311'), (9, 'BCME 315'),
-- Level 300 Second Semester Courses
(9, 'BCME 302'), (9, 'BCME 304'), (9, 'BCME 306'), (9, 'BCME 308'), (9, 'BCME 312'), (9, 'BSMA 302'), (9, 'BSMA 314'), (9, 'BINT 312'),
-- Level 400 First Semester Courses
(9, 'BCME 401'), (9, 'BCME 413'), (9, 'BPSA 301'), (9, 'BCME 400'), (9, 'BCME 419'), (9, 'BINT 403'),
-- Level 400 Second Semester Courses
(9, 'BCME 418'), (9, 'BCME 404'), (9, 'BPSA 412'), (9, 'BCME 400'), (9, 'BCME 414'), (9, 'BCME 422'),

-- BCE
-- Level 100 First Semester Courses
(8, 'BEEE 101'), (8, 'BMAE 101'), (8, 'BCME 101'), (8, 'BMAE 103'), (8, 'UFRE 103'), (8, 'BMAE 105'), (8, 'BSMA 101'), (8, 'BMAE 107'),
-- Level 100 Second Semester Courses
(8, 'UCOM 102'), (8, 'BCME 102'), (8, 'BCME 104'), (8, 'BPSA 102'), (8, 'UFRE 104'), (8, 'BCME 106'), (8, 'BSMA 102'), (8, 'BCME 108'),
-- Level 200 First Semester Courses
(8, 'BCME 201'), (8, 'BCME 203'), (8, 'BEEE 203'), (8, 'BEEE 207'), (8, 'BMAE 205'), (8, 'BMAE 207'), (8, 'BSMA 201'),
-- Level 200 Second Semester Courses
(8, 'BEEE 204'), (8, 'BCME 210'), (8, 'BCME 206'), (8, 'BEEE 208'), (8, 'BCME 202'), (8, 'BSMA 204'), (8, 'BNAS 208'),
-- Level 300 First Semester Courses
(8, 'BCME 303'), (8, 'BCME 309'), (8, 'BCME 301'), (8, 'BCME 311'), (8, 'BCME 305'), (8, 'BEEE 303'), (8, 'BSMA 301'),
-- Level 300 Second Semester Courses
(8, 'BEEE 308'), (8, 'BCME 308'), (8, 'BCME 310'), (8, 'BCME 302'), (8, 'BSMA 302'), (8, 'BCME 306'), (8, 'BCME 304'), (8, 'BSMA 314'), (8, 'BINT 312'),
-- Level 400 First Semester Courses
(8, 'BCME 401'), (8, 'BCME 407'), (8, 'BEEE 409'), (8, 'BPSA 301'), (8, 'BCME 405'), (8, 'BCME 417'), (8, 'BCME 400'),
-- Level 400 Second Semester Courses
(8, 'BCME 408'), (8, 'BCME 410'), (8, 'BEEE 404'), (8, 'BCME 404'), (8, 'BPSA 412'), (8, 'BCME 402');

-- -----------------------------------------------------
-- Table `section`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `fk_class` VARCHAR(10), -- FK
    `fk_course` VARCHAR(10), -- FK
    `credits` INT NOT NULL,
    `level` INT NOT NULL,
    `semester` INT NOT NULL,
    PRIMARY KEY (`id`),
  CONSTRAINT `fk_section_class1` FOREIGN KEY (`fk_class`) REFERENCES `class` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_section_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  -- CONSTRAINT `fk_section_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX section_credits_idx1 ON section (`credits`);
CREATE INDEX section_level_idx1 ON section (`level`);
CREATE INDEX section_semester_idx1 ON section (`semester`);

-- -----------------------------------------------------
-- Table `schedule`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `day_of_week` VARCHAR(10) NOT NULL,
    `course_crdt_hrs` INT DEFAULT 0,
    `start_time` TIME NOT NULL,
    `minutes` INT DEFAULT 50,
    `end_time` TIME GENERATED ALWAYS AS (`start_time` + (`course_crdt_hrs` * `minutes`)),
    `archived` TINYINT(1) DEFAULT 0,
    `fk_section` INT, -- FK
    `fk_room` VARCHAR(10), -- FK
    `fk_semester` INT, -- FK
    PRIMARY KEY (`id`),
  CONSTRAINT `fk_schedule_section1` FOREIGN KEY (`fk_section`) REFERENCES `section` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_room1` FOREIGN KEY (`fk_room`) REFERENCES `room` (`number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX schedule_day_of_week_idx1 ON `schedule` (`day_of_week`);
CREATE INDEX schedule_course_crdt_hrs_idx1 ON `schedule` (`course_crdt_hrs`);
CREATE INDEX schedule_start_time_idx1 ON `schedule` (`start_time`);
CREATE INDEX schedule_minutes_idx1 ON `schedule` (`minutes`);
CREATE INDEX schedule_end_time_idx1 ON `schedule` (`end_time`);
CREATE INDEX schedule_archived_idx1 ON `schedule` (`archived`);

-- -----------------------------------------------------
-- Table `assigned_courses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `assigned_courses`;
CREATE TABLE IF NOT EXISTS `assigned_courses` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `fk_student` VARCHAR(10), -- FK
    `fk_course` VARCHAR(10), -- FK
    `credits` INT NOT NULL,
    `level` INT NOT NULL,
    `semester` INT NOT NULL,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
  CONSTRAINT `fk_assigned_courses_student1` FOREIGN KEY (`fk_student`) REFERENCES `student` (`index_number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_assigned_courses_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX assigned_courses_credits_idx1 ON `assigned_courses` (`credits`);
CREATE INDEX assigned_courses_level_idx1 ON `assigned_courses` (`level`);
CREATE INDEX assigned_courses_semester_idx1 ON `assigned_courses` (`semester`);
CREATE INDEX assigned_courses_added_at_idx1 ON `assigned_courses` (`added_at`);

-- -----------------------------------------------------
-- Table `course_registration`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course_registration`;
CREATE TABLE IF NOT EXISTS `course_registration` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `fk_course` VARCHAR(10), -- FK
    `fk_student` VARCHAR(10), -- FK
    `fk_semester` INT, -- FK
    `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
  CONSTRAINT `fk_course_registration_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_course_registration_student1` FOREIGN KEY (`fk_student`) REFERENCES `student` (`index_number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_course_registration_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX course_registration_added_at_idx1 ON `course_registration` (`added_at`);

-- -----------------------------------------------------
-- Table `staff`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `number` VARCHAR(10) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(255) NOT NULL,
  `middle_name` VARCHAR(255),
  `last_name` VARCHAR(255) NOT NULL,
  `prefix` VARCHAR(10),
  `gender` VARCHAR(1) DEFAULT 'F',
  `role` VARCHAR(15) NOT NULL,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_department` INT NULL,
  PRIMARY KEY (`number`),
  CONSTRAINT `fk_staff_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX staff_email_idx1 ON `staff` (`email`);
CREATE INDEX staff_first_name_idx1 ON `staff` (`first_name`);
CREATE INDEX staff_last_name_idx1 ON `staff` (`last_name`);
CREATE INDEX staff_gender_idx1 ON `staff` (`gender`);
CREATE INDEX staff_role_idx1 ON `staff` (`role`);
CREATE INDEX staff_archived_idx1 ON `staff` (`archived`);

-- -----------------------------------------------------
-- Table `lecture`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lecture`;
CREATE TABLE IF NOT EXISTS `lecture` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `archived` TINYINT(1) DEFAULT 0,
  `fk_staff` VARCHAR(20) NULL,
  `fk_section` INT NULL,
  `fk_semester` INT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_lecture_staff1` FOREIGN KEY (`fk_staff`) REFERENCES `staff` (`number`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_lecture_section1` FOREIGN KEY (`fk_section`) REFERENCES `section` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `lecture_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX lecture_archived_idx1 ON `lecture` (`archived`);

-- -----------------------------------------------------
-- Table `exam_results`
-- Stores published exam results per student per course per semester.
-- Grades follow the standard Ghanaian university grading scale.
-- -----------------------------------------------------
DROP TABLE IF EXISTS `exam_results`;
CREATE TABLE IF NOT EXISTS `exam_results` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `score` DECIMAL(5,2) NOT NULL,
    `grade` VARCHAR(2) NOT NULL,
    `grade_point` DECIMAL(3,1) NOT NULL,
    `published` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `fk_student` VARCHAR(10) NOT NULL,
    `fk_course` VARCHAR(10) NOT NULL,
    `fk_semester` INT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_student_course_semester` (`fk_student`, `fk_course`, `fk_semester`),
    CONSTRAINT `fk_exam_results_student1` FOREIGN KEY (`fk_student`) REFERENCES `student` (`index_number`) ON DELETE NO ACTION ON UPDATE CASCADE,
    CONSTRAINT `fk_exam_results_course1` FOREIGN KEY (`fk_course`) REFERENCES `course` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
    CONSTRAINT `fk_exam_results_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX exam_results_score_idx1 ON `exam_results` (`score`);
CREATE INDEX exam_results_grade_idx1 ON `exam_results` (`grade`);
CREATE INDEX exam_results_published_idx1 ON `exam_results` (`published`);

-- -----------------------------------------------------
-- Table `timetable_files`
-- Stores admin-uploaded timetable PDF files per semester.
-- -----------------------------------------------------
DROP TABLE IF EXISTS `timetable_files`;
CREATE TABLE IF NOT EXISTS `timetable_files` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `archived` TINYINT(1) DEFAULT 0,
    `fk_semester` INT NOT NULL,
    `fk_department` INT,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_timetable_files_semester1` FOREIGN KEY (`fk_semester`) REFERENCES `semester` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    CONSTRAINT `fk_timetable_files_department1` FOREIGN KEY (`fk_department`) REFERENCES `department` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX timetable_files_archived_idx1 ON `timetable_files` (`archived`);