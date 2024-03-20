<?php

namespace Src\Controller;

use Src\Core\Database;

class Course
{
    private $dm;

    public function __construct($config, $dbServer = "mysql", $user = "root", $pass = "")
    {
        $this->dm = new Database($config, $dbServer, $user, $pass);
    }

    public function courseInfo(string $course_code)
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits`, 
        cs.`semester` AS semester, cs.`level`, cc.`id` AS category_id, 
        cc.`name` AS category_name, d.`name` AS department_name 
        FROM 
        course AS cs, course_category AS cc, department AS d 
        WHERE 
        cs.`fk_category` = cc.`id` AND cs.`fk_department` = d.`id` AND cs.`code` = :c";
        return $this->dm->run($query, array(':c' => $course_code))->all();
    }
}
