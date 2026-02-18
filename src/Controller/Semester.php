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
}
