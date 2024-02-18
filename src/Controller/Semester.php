<?php

namespace Src\Controller;

use Src\Core\Database;

class Semester
{
    private $dm;

    public function __construct($config, $dbServer = "mysql", $user = "root", $pass = "")
    {
        $this->dm = new Database($config, $dbServer, $user, $pass);
    }

    public function semesterCourses($semester): mixed
    {
        $query = "SELECT * FROM course WHERE fk_semester = :s";
        $data = $this->dm->run($query, array(':s' => $semester))->one();
    }

    public function currentSemester(): mixed
    {
        $query = "SELECT s.`id` AS semester_id, s.`name` AS semester_name, s.`course_registration_opened` AS reg_open_status, 
         s.`registration_end` AS reg_end_date, a.`id` AS academic_year_id, a.`name` AS academic_year_name 
         FROM semester AS s, academic_year AS a WHERE s.`fk_academic_year` = a.`id` AND s.`active` = 1 AND a.`active` = 1";
        return $this->dm->run($query)->one();
    }
}
