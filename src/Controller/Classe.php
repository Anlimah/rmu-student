<?php

namespace Src\Controller;

use Src\Core\Database;

class Classe
{
    private $dm;

    public function __construct($config, $dbServer, $user, $pass)
    {
        $this->dm = new Database($config, $dbServer, $user, $pass);
    }

    public function fetchCompulsoryCourses($class, $semester)
    {
        $query = "SELECT 
        cs.`code` AS course_code, cs.`name` AS course_name, cs.`credits`, 
        cs.`semester` AS course_semester, cs.`level` AS course_level, c.`code` AS class_code, 
        cc.`id` AS course_category_id, cc.`name` AS course_category_name 
        FROM student AS s, department AS d, programs AS p, class AS c, course AS cs, course_category AS cc 
        WHERE s.`fk_department` = d.`id` AND s.`fk_program` = p.`id` AND 
        s.`fk_class` = c.`code` AND cs.`fk_category` = cc.`id` AND c.`fk_program` = p.`id` AND 
        cd.`fk_department` = s.`fk_department` AND p.`id` = :p";
        return $this->dm->run($query, array(':p' => $class, ':s' => $semester))->all();
    }
}
