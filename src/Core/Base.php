<?php

namespace Src\Core;

use Src\Core\Functions;

class Base
{
    public static function dd($data)
    {
        echo "<pre>";
        echo var_dump($data);
        echo "</pre>";
        die();
    }

    public static function build_path($path)
    {
        return Functions::BASE_PATH . str_replace("/", DIRECTORY_SEPARATOR, $path);
    }

    public static function abort($status = 404)
    {
        http_response_code($status);
        die("Page Not Found");
    }
}
