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

    public static function shortenText(string $text, int $max = 17): string
    {
        if (strlen($text) <= $max) return $text;
        $shortenedText = substr($text, 0, $max);
        return $shortenedText . '...';
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

    public static function killSession(): bool
    {
        session_destroy();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
            return true;
        }
        return false;
    }

    public static function sessionExpire(): bool
    {
        if (!isset($_SESSION["lastAccessed"])) $_SESSION["lastAccessed"] = time();
        $_SESSION["currentAccess"] = time();
        $diff = $_SESSION["currentAccess"] - $_SESSION["lastAccessed"];

        if ($diff > 1800) {
            if (self::killSession()) $_SESSION = array();
            return true;
        }
        $_SESSION["lastAccessed"] = time();
        return false;
    }

    public static function logout()
    {
        self::killSession();
        $_SESSION = array();
        header('Location: login.php');
    }
}
