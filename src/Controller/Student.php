<?php

namespace Src\Controller;

use Src\Core\Database;

class Student
{
    private $dm;

    public function __construct($config)
    {
        $this->dm = new Database($config);
    }

    public function login($index_number, $password)
    {
        $sql = "SELECT * FROM `student` WHERE `index_number` = :u";
        $data = $this->dm->run($sql, array(':u' => $index_number))->one();
        if (!empty($data) && password_verify($password, $data["password"]))
            return array("success" => true, "message" => $data);
        return array("success" => false, "message" => "Incorrect index number or password!");
    }

    public function createNewPassword($index_number, $password)
    {
        $is_old = $this->login($index_number, $password);
        if ($is_old["success"]) return array("success" => false, "message" => "Please create a new password to continue!");
        $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE `student` SET `password` = :p, `default_password` = 0 WHERE `index_number` = :u";
        $data = $this->dm->run($sql, array(':p' => $hashed_pass, ':u' => $index_number))->edit();
        if (!empty($data)) return array(
            "success" => true,
            "message" => "New password created successfully!",
            "data" => array(
                "index_number" => $index_number
            )
        );
        return array("success" => false, "message" => "New password creation failed!");
    }

    public function getClassAssignedCourses($class_code): mixed
    {
        $class_student_courses = $this->dm->run(
            "SELECT `fk_course` AS course, `credit_hours`, `level`, `semester` FROM `section` WHERE `fk_class` = :c",
            array(":c" => $class_code)
        );
        if (empty($class_student_courses)) return array(
            "success" => false,
            "message" => "No courses assigned to your class yet. Please contact your HOD for more details."
        );
        return array("success" => true, "data" => $class_student_courses);
    }

    public function assignStudentCourses($index_number, array $courses): mixed
    {
        $courses_assigned = 0;
        foreach ($courses as $course) {
            $courses_assigned += $this->dm->run(
                "INSERT INTO `student_course_assignments` (`fk_course`, `fk_student`, `credit_hours`, `level`, `semester`) 
                VALUES (:fkc, :fks, :c, :l, :s)",
                array(
                    ":fkc" => $course["course"],
                    ":fks" => $index_number,
                    ":c" => $course["credit_hours"],
                    ":l" => $course["level"],
                    ":s" => $course["semester"]
                )
            )->add();
        }
        return $courses_assigned;
    }

    public function getCoursesByClassLevelSemester($class_code, $level, $semester): mixed
    {
        $class_student_courses = $this->dm->run(
            "SELECT `fk_course` AS course, `credit_hours`, `level`, `semester` FROM `section` 
            WHERE `fk_class` = :c AND `level` = :l AND `semester` = :s",
            array(":c" => $class_code, ":l" => $level, ":s" => $semester)
        )->all();
        if (empty($class_student_courses)) return array(
            "success" => false,
            "message" => "No courses assigned to your class yet. Please contact your HOD for more details."
        );
        return array("success" => true, "data" => $class_student_courses);
    }

    public function getCurrentLevel($index_number): mixed
    {
        return $this->dm->run(
            "SELECT * FROM `level` WHERE `fk_student` = :fks AND `active` = 1",
            array(":fks" => $index_number)
        )->one();
    }

    public function setupAccount($data): mixed
    {
        $total_semesters = $this->dm->run(
            "SELECT COUNT(`id`) AS `total_semesters` FROM `level` WHERE `fk_student` = :fks",
            array(":fks" => $data["index_number"])
        )->one()["total_semesters"];
        if ($total_semesters) return array(
            "success" => true,
            "message" => "Program setup was successful!",
            "data" => $total_semesters
        );

        $semester_count = $data["programme_duration"] * 2;
        $sem_count = 0;
        $total_student_courses = 0;

        for ($semester = 1; $semester <= $semester_count; $semester++) {
            $active = 0;
            if ($semester === 1) $active = 1; // set the first semester as active
            $sem = ($semester % 2 == 0) ? 2 : 1;

            if ($semester === 3 || $semester === 5 || $semester === 7) $data["level_admitted"] += 100;

            // $assigned_level_courses = $this->getCoursesByClassLevelSemester($data["class"], $data["level_admitted"], $sem);
            // if (!$assigned_level_courses["success"]) return $assigned_level_courses;
            // //return $assigned_level_courses["data"];
            // $total_student_courses += $this->assignStudentCourses($data["index_number"], $assigned_level_courses["data"]);

            $sem_count += $this->dm->run(
                "INSERT INTO `level` (`level`, `semester`, `active`, `fk_student`) VALUES (:l, :s, :a, :fks)",
                array(":l" => $data["level_admitted"], ":s" => $sem, ":a" => $active, ":fks" => $data["index_number"])
            )->add();
        }

        if ($sem_count === $semester_count) {
            $current_level = $this->getCurrentLevel($data["index_number"]);
            return array(
                "success" => true,
                "message" => "Program setup was successful!",
                "data" => array(
                    "current_level" => $current_level,
                    "semesters" => $sem_count,
                    "courses_assigned" => $total_student_courses
                )
            );
        }

        return array("success" => false, "message" => "Failed to setup program. Kindly contact registry!");
    }

    public function registerSemesterCourses(array $courses, $student, $semester): mixed
    {
        $registered_courses = 0;
        $errorMessages = [];

        foreach ($courses as $course) {
            // Check if the course is assigned to the student and also check if it is registered
            $selectQuery = "SELECT * FROM `student_courses` WHERE `fk_course` = :fkc AND `fk_student` = :fks AND `fk_semester` = :fkm";
            $studentCourseData = $this->dm->run($selectQuery, array(':fkc' => $course, ':fks' => $student, ':fkm' => $semester))->one();
            if (empty($studentCourseData)) {
                array_push($errorMessages, ["course" => $course, "message" => "Course ($course) is not assigned to you."]);
            } else if ($studentCourseData['registered'] == 1) {
                array_push($errorMessages, ["course" => $course, "message" => "Course ($course) is already registered."]);
            } else {
                $query = "UPDATE `student_courses` SET `registered` = 1 WHERE `fk_course` = :fkc AND `fk_student` = :fks AND `fk_semester` = :fkm";
                $registered_courses += $this->dm->run($query, array(':fkc' => $course, ':fks' => $student, ':fkm' => $semester))->add();
            }
        }

        if (!empty($errorMessages)) {
            return array(
                "success" => false,
                "message" => "Some courses could not be registered.",
                "errors" => $errorMessages,
                "registered_count" => $registered_courses
            );
        }
        return array(
            "success" => true,
            "message" => "Successfully registered {$registered_courses} course(s).",
            "registered_count" => $registered_courses
        );
    }

    public function resetCourseRegistration($student, $semester): mixed
    {
        $query = "UPDATE `student_courses` SET `registered` = 0 WHERE `fk_student` = :fks AND `fk_semester` = :fkm";
        return $this->dm->run($query, array(':fks' => $student, ':fkm' => $semester))->del();
    }

    public function fetchCourseRegistrationSummary($student, $semester): mixed
    {
        $query = "SELECT COUNT(cr.`id`) AS total_course, SUM(c.`credit_hours`) AS total_credit 
        FROM `student_courses` AS cr, `course` AS c 
        WHERE cr.`fk_course` = c.`code` AND cr.`fk_student` = :fks AND cr.`fk_semester` = :fkm";
        return $this->dm->run($query, array(':fks' => $student, ':fkm' => $semester))->one();
    }

    public function fetchData($index_number): mixed
    {
        $query = "SELECT 
        s.`index_number`, s.`app_number`, s.`email`, s.`phone_number`, s.`prefix`, s.`first_name`, 
        s.`middle_name`, s.`last_name`, s.`suffix`, s.`gender`, s.`dob`, s.`nationality`, 
        s.`photo`, s.`marital_status`, s.`disability`, s.`date_admitted`, s.`term_admitted`, s.`stream_admitted`, 
        s.`level_admitted`, CONCAT(s.`first_name`, ' ', IFNULL(s.`middle_name`, ''), ' ', s.`last_name`) AS full_name, 
        d.`id` AS department_id, d.`name` AS department_name, p.`id` AS program_id, p.`code`, p.`name` AS program_name, c.`code` AS class_code 
        FROM student AS s, department AS d, programs AS p, class AS c 
        WHERE s.`fk_department` = d.`id` AND s.`fk_program` = p.`id` AND 
        s.`fk_class` = c.`code` AND s.`index_number` = :i";
        return $this->dm->run($query, array(':i' => $index_number))->one();
    }

    public function fetchRegisteredUnregisteredCoursesForCurrent($index_number, $level, $semester): mixed
    {
        $query = "SELECT 
                cs.`code` AS course_code,
                cs.`name` AS course_name,
                cs.`credit_hours` AS credits,
                cs.`level`,
                cs.`semester`,
                cc.`id` AS category_id,
                cc.`name` AS category_name,
                COALESCE(ac.`registered`, 0) AS registered,
                ac.`id` AS student_course_id
            FROM `student_courses` AS ac
            LEFT JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id`
            LEFT JOIN `courses` AS cs ON ac.`fk_course` = cs.`code`
            WHERE cs.`level` = :l AND ac.`fk_student` = :i AND ac.`fk_semester` = :s AND cs.`semester` = :s
            ORDER BY cs.`code` ASC
            ";

        $courses = $this->dm->run($query, [':i' => $index_number, ':l' => $level, ':s' => $semester])->all();
        return $courses;
    }

    public function fetchUnregisteredCoursesForPrevious($index_number, $level, $semester, $semester_id): mixed
    {
        $query = "SELECT 
            cs.`code` AS course_code, 
            cs.`name` AS course_name, 
            cs.`credit_hours` AS credits, 
            cs.`level`, 
            cs.`semester`, 
            cc.`id` AS category_id, 
            cc.`name` AS category_name, 
            ac.`registered` 
            FROM `course` AS cs 
            LEFT JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id` 
            LEFT JOIN `student_courses` AS ac ON ac.`fk_course` = cs.`code` 
            LEFT JOIN `student` AS st ON ac.`fk_student` = st.`index_number` 
            WHERE ac.`fk_student` = st.`index_number` AND ac.`fk_semester` = :fks AND st.`index_number` = :i AND cs.`level` < :l AND cs.`semester` = :s 
        ";
        $courses = $this->dm->run($query, array(':i' => $index_number, ':l' => $level, ':s' => $semester, ':fks' => $semester_id))->all();
        return $courses;
    }

    public function fetchRegisteredCoursesForCurrent($index_number, $semester_id): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credit_hours` AS credits, 
        cs.`level`, cs.`semester`, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM `student_courses` AS cr, `course` AS cs, `course_category` AS cc 
        WHERE cr.`fk_course` = cs.`code` AND cc.`id` = cs.`fk_category` AND cr.`fk_student` = :fks AND cr.`fk_semester` = :fkm";
        return $this->dm->run($query, array(':fks' => $index_number, ':fkm' => $semester_id))->all();
    }

    public function fetchSemesterCourses($index_number, $level, $semester): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cc.`id` AS category_id, cc.`name` AS category_name 
        FROM `course` AS cs, `course_category` AS cc, `student_courses` AS ac, `student` AS st 
        WHERE 
        ac.`fk_course` = cs.`code` AND ac.`fk_student` = st.`index_number` AND cs.`fk_category` = cc.`id` AND 
        st.`index_number` = :i AND cs.`level` = :l AND cs.`semester` = :s";
        return $this->dm->run($query, array(':i' => $index_number, ':l' => $level, ':s' => $semester))->all();
    }

    public function fetchSemesterCompulsoryCourses($index_number, $semester): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr, `course` AS cs, `course_category` AS cc, class AS cl, semester AS sm, student AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` 
        AND st.`fk_class` = cl.`code` AND st.`index_number` = :i AND sm.`id` = :s AND cc.`name` = 'compulsory'";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchSemesterElectiveCourses($index_number, $semester): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr, course AS cs, course_category AS cc, class AS cl, semester AS sm, student AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` 
        AND st.`fk_class` = cl.`code` AND st.`index_number` = :i AND sm.`id` = :s AND cc.`name` = 'elective'";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchCoursesBySemName(string $index_number, int $semester = 1): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credit_hours` AS credits, cs.`level`, cs.`semester`,  
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr, `course` AS co, `course_category` AS cc, `semester` AS sm, `student` AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND cr.`fk_semester` = sm.`id` AND 
        cs.`fk_category` = cc.`id` AND st.`index_number` = :i AND sm.`name` = :s";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchRegCoursesBySemester(string $index_number, int $semester_id, int $semester_name): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr 
        JOIN `course` AS cs ON cr.`fk_course` = cs.`code` 
        JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id` 
        JOIN `semester` AS sm ON cr.`fk_semester_registered` = sm.`id` 
        JOIN `student` AS st ON cr.`fk_student` = st.`index_number` 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester_registered` = sm.`id` AND cs.`fk_category` = cc.`id` AND 
        st.`index_number` = :i AND sm.`name` = :sn AND cr.`fk_semester_registered` = :srid AND cr.`registered` = 1";
        return $this->dm->run($query, array(
            ':i' => $index_number,
            ':sn' => $semester_name,
            ':srid' => $semester_id
        ))->all();
    }

    public function fetchUnregCoursesBySemester(string $index_number, int $semester_id, int $semester_name): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr 
        JOIN `course` AS cs ON cr.`fk_course` = cs.`code`
        JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id`
        JOIN `semester` AS sm ON cr.`fk_semester_registered` = sm.`id`
        JOIN `student` AS st ON cr.`fk_student` = st.`index_number` 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester_registered` = sm.`id` AND cs.`fk_category` = cc.`id` AND 
        st.`index_number` = :i AND sm.`name` = :sn AND cr.`fk_semester_registered` = :srid AND cr.`registered` = 0";
        return $this->dm->run($query, array(
            ':i' => $index_number,
            ':sn' => $semester_name,
            ':srid' => $semester_id
        ))->all();
    }

    public function fetchRegOrUnregCourses(string $index_number, int $registered = 0): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr, `course` AS cs, `course_category` AS cc, `semester` AS sm, `student` AS st 
        WHERE 
        cr.`fk_course` = cs.`code`AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` AND 
        cr.`registered` = :r AND st.`index_number` = :i";
        return $this->dm->run($query, array(':i' => $index_number, ':r' => $registered))->all();
    }

    public function fetchCoursesBySemesterAndLevel(int $semester, int $level, int $department, int $registered = 0): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credit_hours` AS credits, cs.`level`, cs.`semester`,  
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `student_courses` AS cr, `course` AS co, `course_category` AS cc, 
        `semester` AS sm, `student` AS st, `department` AS d 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` AND cs.`fk_department` = d.`id` AND 
        d.`id` = :d AND sm.`name` = :s AND cs.`level` <= :l AND cr.`registered` = :r";
        return $this->dm->run(
            $query,
            array(
                ':d' => $department,
                ':s' => $semester,
                ':l' => $level,
                ':r' => $registered
            )
        )->all();
    }

    public function  fetchCoursesBySemAndLevel(string $index_number, int $level, int $current_semester_name): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credit_hours` AS credits, cs.`level`, cs.`semester`, 
        cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course` AS cs, `course_category` AS cc, `student_courses` AS ac, `student` AS st 
        WHERE 
        ac.`fk_course` = cs.`code` AND ac.`fk_student` = st.`index_number` AND cs.`fk_category` = cc.`id` AND 
        st.`index_number` = :i AND cs.`level` < :l AND cs.`semester` = :s AND cs.`code` NOT IN (
            SELECT ac.`fk_course` FROM `student_courses` AS ac WHERE ac.`fk_student` = :i
        )";
        return $this->dm->run($query, array(':i' => $index_number, ':l' => $level, ':s' => $current_semester_name))->all();
    }
}
