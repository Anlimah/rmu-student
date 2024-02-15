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
        if (empty($data)) return 0;
        if (password_verify($password, $data["password"])) return $data;
    }

    public function fetchData(): mixed
    {
        $query = "SELECT * FROM student WHERE index_number = :i";
    }

    public function fetchSemesterCourses(): mixed
    {
        $query = "SELECT * FROM course WHERE fk_semester = :i";
    }
}
