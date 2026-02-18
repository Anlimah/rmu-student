<?php

namespace Src\Controller;

use Src\Core\Database;

class Course
{
    private $dm;

    public function __construct($config)
    {
        $this->dm = new Database($config);
    }
}
