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

    public function fetchData($index_number): mixed
    {
        $query = "SELECT 
        s.`index_number`, s.`app_number`, s.`email`, s.`phone_number`, s.`prefix`, s.`first_name`, 
        s.`middle_name`, s.`last_name`, s.`suffix`, s.`gender`, s.`dob`, s.`nationality`, 
        s.`photo`, s.`marital_status`, s.`disability`, s.`date_admitted`, s.`term_admitted`, s.`stream_admitted`, 
        s.`level_admitted`, CONCAT(s.`first_name`, ' ', IFNULL(s.`middle_name`, ''), ' ', s.`last_name`) AS full_name, 
        ay.`id` AS academic_year_id, ay.`name` AS academic_year_name, d.`id` AS department_id, d.`name` AS department_name, 
        p.`id` AS program_id, p.`program_code`, p.`name` AS program_name, c.`code` AS class_code 
        FROM student AS s, academic_year AS ay, department AS d, programs AS p, class AS c 
        WHERE s.`fk_academic_year` = ay.`id` AND s.`fk_department` = d.`id` AND s.`fk_program` = p.`id` AND 
        s.`fk_class` = c.`code` AND index_number = :i";
        return $this->dm->run($query, array(':i' => $index_number))->one();
    }

    public function fetchSemesterCourses(): mixed
    {
        $query = "SELECT * FROM course WHERE fk_semester = :i";
    }

    public function courseRegistrationStatus(): mixed
    {
        $query = "SELECT * FROM semester WHERE `active` = 1";
    }
}
