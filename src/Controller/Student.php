<?php

namespace Src\Controller;

use Src\Core\Database;

class Student
{
    private $dm;

    public function __construct($config, $dbServer = "mysql", $user = "root", $pass = "")
    {
        $this->dm = new Database($config, $dbServer, $user, $pass);
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
        if (!empty($data)) return array("success" => true, "message" => "New password created successfully!", "data" => $index_number);
        return array("success" => false, "message" => "New password creation failed!");
    }

    public function setupSemester($index_number): mixed
    {
        // add student semester courses to course registration
        // get current semester courses for level 100

        $q1 = "SELECT * FROM course AS cs, curriculum AS cr, programs AS pg, student AS st 
        WHERE cr.`fk_course` = cs.`code`AND cr.`fk_program` = pg.`id` AND pg.`id` = st.`fk_program` AND st.`index_number` = :i";
        $q1_result = $this->dm->run($q1, array(":i" => $index_number))->all();
        //return array("success" => false, "message" => $q1_result);
        if (!empty($q1_result)) {
            $q2 = "SELECT `id` FROM semester WHERE `active` = 1";
            $q2_result = $this->dm->run($q2)->one();
            //return array("success" => false, "message" => $q2_result[0]["id"]);
            if (!empty($q2_result)) {
                foreach ($q1_result as $course) {
                    //return array("success" => false, "message" => $course);
                    $q3 = "INSERT INTO `course_registration` (`fk_course`, `fk_student`, `fk_semester`, `level`, `semester`) 
                        VALUES (:fkc, :fks, :fkt, :l, :s)";
                    $params3 = array(
                        ":fkc" => $course["code"],
                        ":fks" => $data["index_number"],
                        ":fkt" => $q2_result[0]["id"],
                        ":l" => 100,
                        ":s" => 1
                    );
                    $this->dm->run($q3, $params3)->add();
                }
            }
        }
        // add student semester courses to course registration
        // get current semester courses for level 100
        /*$q1 = "SELECT * FROM course WHERE `semester` = 1 AND `level` = 100 AND fk_department = :d";
        $q1_result = $this->dm->run($q1, array(":d" => $data["department"]))->all();
        //return array("success" => false, "message" => $q1_result);
        if (!empty($q1_result)) {
            $q2 = "SELECT `id` FROM semester WHERE `active` = 1";
            $q2_result = $this->dm->run($q2)->one();
            //return array("success" => false, "message" => $q2_result[0]["id"]);
            if (!empty($q2_result)) {
                foreach ($q1_result as $course) {
                    //return array("success" => false, "message" => $course);
                    $q3 = "INSERT INTO `course_registration` (`fk_course`, `fk_student`, `fk_semester`, `level`, `semester`) 
                        VALUES (:fkc, :fks, :fkt, :l, :s)";
                    $params3 = array(
                        ":fkc" => $course["code"],
                        ":fks" => $data["index_number"],
                        ":fkt" => $q2_result[0]["id"],
                        ":l" => 100,
                        ":s" => 1
                    );
                    $this->dm->run($q3, $params3)->add();
                }
            }
        }*/
    }

    public function registerSemesterCourses(array $courses, $student, $semester): mixed
    {
        $registered_courses = 0;
        foreach ($courses as $course) {
            //return $course . ' ' . $student . ' ' . $semester;
            $query = "UPDATE `course_registration` SET `registered` = 1,  `fk_semester_registered` = :fkm
            WHERE `fk_course` = :fkc AND `fk_student` = :fks";

            $registered_courses += $this->dm->run($query, array(':fkc' => $course, ':fks' => $student, ':fkm' => $semester))->edit();
        }
        return $registered_courses;
    }

    public function resetCourseRegistration($student, $semester): mixed
    {
        //return $course . ' ' . $student . ' ' . $semester;
        $query = "UPDATE `course_registration` SET `registered` = 0, `fk_semester_registered` = NULL 
        WHERE `fk_student` = :fks AND `fk_semester` = :fkm";
        return $this->dm->run($query, array(':fks' => $student, ':fkm' => $semester))->edit();
    }

    public function fetchCourseRegistrationSummary($student, $semester): mixed
    {
        $query = "SELECT COUNT(cr.`id`) AS total_course, SUM(c.`credits`) AS total_credit 
        FROM `course_registration` AS cr, `course` AS c 
        WHERE cr.`fk_course` = c.`code` AND cr.`fk_student` = :fks AND cr.`fk_semester_registered` = :fkm AND cr.`registered` = 1";
        return $this->dm->run($query, array(':fks' => $student, ':fkm' => $semester))->one();
    }

    public function fetchData($index_number): mixed
    {
        $query = "SELECT 
        s.`index_number`, s.`app_number`, s.`email`, s.`phone_number`, s.`prefix`, s.`first_name`, 
        s.`middle_name`, s.`last_name`, s.`suffix`, s.`gender`, s.`dob`, s.`nationality`, 
        s.`photo`, s.`marital_status`, s.`disability`, s.`date_admitted`, s.`term_admitted`, s.`stream_admitted`, 
        s.`level_admitted`, CONCAT(s.`first_name`, ' ', IFNULL(s.`middle_name`, ''), ' ', s.`last_name`) AS full_name, 
        ay.`id` AS academic_year_id, ay.`name` AS academic_year_name, d.`id` AS department_id, d.`name` AS department_name, 
        p.`id` AS program_id, p.`code`, p.`name` AS program_name, c.`code` AS class_code 
        FROM student AS s, academic_year AS ay, department AS d, programs AS p, class AS c 
        WHERE s.`fk_academic_year` = ay.`id` AND s.`fk_department` = d.`id` AND s.`fk_program` = p.`id` AND 
        s.`fk_class` = c.`code` AND s.`index_number` = :i";
        return $this->dm->run($query, array(':i' => $index_number))->one();
    }

    public function fetchSemesterCourses($index_number, $semester): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr, `course` AS cs, `course_category` AS cc, `semester` AS sm, `student` AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND cr.`fk_semester` = sm.`id` AND 
        cs.`fk_category` = cc.`id` AND st.`index_number` = :i AND sm.`id` = :s";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchSemesterCompulsoryCourses($index_number, $semester): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr, `course` AS cs, `course_category` AS cc, class AS cl, semester AS sm, student AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_class` = cl.`code` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` 
        AND st.`fk_class` = cl.`code` AND st.`index_number` = :i AND sm.`id` = :s AND cc.`name` = 'compulsory'";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchSemesterElectiveCourses($index_number, $semester): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr, course AS cs, course_category AS cc, class AS cl, semester AS sm, student AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_class` = cl.`code` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` 
        AND st.`fk_class` = cl.`code` AND st.`index_number` = :i AND sm.`id` = :s AND cc.`name` = 'elective'";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchCoursesBySemName(string $index_number, int $semester = 1): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credits` AS credits, cs.`level`, cs.`semester`,  
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr, `course` AS co, `course_category` AS cc, `semester` AS sm, `student` AS st 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND cr.`fk_semester` = sm.`id` AND 
        cs.`fk_category` = cc.`id` AND st.`index_number` = :i AND sm.`name` = :s";
        return $this->dm->run($query, array(':i' => $index_number, ':s' => $semester))->all();
    }

    public function fetchRegCoursesBySemester(string $index_number, int $semester_id, int $semester_name): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr 
        JOIN `course` AS cs ON cr.`fk_course` = cs.`code` 
        JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id` 
        JOIN `semester` AS sm ON cr.`fk_semester_registered` = sm.`id` 
        JOIN `student` AS st ON cr.`fk_student` = st.`index_number` 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester_registered` = sm.`id` AND cs.`fk_category` = cc.`id` AND 
        st.`index_number` = :i AND sm.`name` = :sn AND cr.`fk_semester_registered` = :srid AND cr.`registered` = 1";
        return $this->dm->run($query, array(
            ':i' => $index_number, ':sn' => $semester_name, ':srid' => $semester_id
        ))->all();
    }

    public function fetchUnregCoursesBySemester(string $index_number, int $semester_id, int $semester_name): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr
        JOIN `course` AS cs ON cr.`fk_course` = cs.`code`
        JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id`
        JOIN `semester` AS sm ON cr.`fk_semester_registered` = sm.`id`
        JOIN `student` AS st ON cr.`fk_student` = st.`index_number` 
        WHERE 
        cr.`fk_course` = cs.`code` AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester_registered` = sm.`id` AND cs.`fk_category` = cc.`id` AND 
        st.`index_number` = :i AND sm.`name` = :sn AND cr.`fk_semester_registered` = :srid AND cr.`registered` = 0";
        return $this->dm->run($query, array(
            ':i' => $index_number, ':sn' => $semester_name, ':srid' => $semester_id
        ))->all();
    }

    public function fetchRegOrUnregCourses(string $index_number, int $registered = 0): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr, `course` AS cs, `course_category` AS cc, `semester` AS sm, `student` AS st 
        WHERE 
        cr.`fk_course` = cs.`code`AND cr.`fk_student` = st.`index_number` AND 
        cr.`fk_semester` = sm.`id` AND cs.`fk_category` = cc.`id` AND 
        cr.`registered` = :r AND st.`index_number` = :i";
        return $this->dm->run($query, array(':i' => $index_number, ':r' => $registered))->all();
    }

    public function fetchCoursesBySemesterAndLevel(int $semester, int $level, int $department, int $registered = 0): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name,  cs.`credits` AS credits, cs.`level`, cs.`semester`,  
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
        `course_registration` AS cr, `course` AS co, `course_category` AS cc, 
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

    public function  fetchCoursesBySemAndLevel(string $index_number, int $current_semester_id, int $current_semester_name, int $level, int $registered = 0): mixed
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits` AS credits, cs.`level`, cs.`semester`, 
        cr.`registered` AS reg_status, cc.`id` AS category_id, cc.`name` AS category_name 
        FROM 
            `course_registration` AS cr 
            JOIN `course` AS cs ON cr.`fk_course` = cs.`code` 
            JOIN `course_category` AS cc ON cs.`fk_category` = cc.`id` 
            JOIN `semester` AS sm ON cr.`fk_semester` = sm.`id` 
            JOIN `student` AS st ON cr.`fk_student` = st.`index_number` 
        WHERE 
            sm.`name` = :s 
            AND cs.`level` < :l 
            AND (
                cr.`registered` = :r 
                OR (
                    cr.`fk_course` NOT IN (
                        SELECT cr2.`fk_course` 
                        FROM `course_registration` AS cr2 
                        JOIN `semester` AS sm2 ON cr2.`fk_semester` = sm2.`id` 
            			JOIN `student` AS st2 ON cr2.`fk_student` = st2.`index_number` 
                        WHERE cr2.`fk_student` = st2.`index_number` AND sm2.`id` != :cs
                    )
                    AND sm.`id` = :cs
                )
            )
            AND st.`index_number` = :i";
        return $this->dm->run($query, array(
            ':s' => $current_semester_name, ':cs' => $current_semester_id, ':l' => $level, ':r' => $registered, ':i' => $index_number
        ))->all();
    }
}
