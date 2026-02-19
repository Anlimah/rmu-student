<?php

namespace Src\Controller;

use Src\Core\Database;

class Semester
{
    private $dm;

    public function __construct($config)
    {
        $this->dm = new Database($config);
    }

    public function currentSemester(): mixed
    {
        $query = "SELECT
        s.`id` AS semester_id, s.`name` AS semester_name, s.`course_registration_opened` AS reg_open_status,
        s.`registration_end` AS reg_end_date, a.`id` AS academic_year_id, a.`name` AS academic_year_name
        FROM
        `semester` AS s, `academic_year` AS a
        WHERE
        s.`fk_academic_year` = a.`id` AND s.`active` = 1 AND a.`active` = 1";
        return $this->dm->run($query)->one();
    }

    public function allSemesters(): mixed
    {
        $query = "SELECT
            s.`id` AS semester_id, s.`name` AS semester_name,
            s.`exam_results_uploaded`,
            a.`id` AS academic_year_id, a.`name` AS academic_year_name
            FROM `semester` AS s
            JOIN `academic_year` AS a ON s.`fk_academic_year` = a.`id`
            WHERE s.`archived` = 0 AND a.`archived` = 0
            ORDER BY a.`start_year` DESC, s.`name` ASC";
        return $this->dm->run($query)->all();
    }
}
