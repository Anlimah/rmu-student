<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Make environment variables available to getenv()
foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
}
define("ROOT_DIR", dirname(__FILE__, 1));
