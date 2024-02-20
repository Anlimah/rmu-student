<?php
session_start();

if (!isset($_SESSION["lastAccessed"])) $_SESSION["lastAccessed"] = time();
$_SESSION["currentAccess"] = time();

$diff = $_SESSION["currentAccess"] - $_SESSION["lastAccessed"];

if ($diff >  1800) die(json_encode(array("success" => false, "message" => "logout")));

/*
* Designed and programmed by
* @Author: Francis A. Anlimah
*/

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "../bootstrap.php";
$config = require('../config/database.php');

use Src\Controller\Programs;
use Src\Controller\Student;
use Src\Core\Base;
use Src\Core\Validator;
use Src\Controller\Courses;
use Src\Controller\Classes;
use Src\Controller\Semester;

$fullUrl = $_SERVER["REQUEST_URI"];
$urlParse = parse_url($fullUrl, PHP_URL_PATH);
$urlPath = str_replace("/rmu-student/api/", "", $urlParse);
$separatePath = explode("/", $urlPath);
$resourceRequested = count($separatePath);

$module = $separatePath[0];

// All GET request will be sent here
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $action = $separatePath[1];

    if ($module === 'student') {
        $studentObj = new Student($config["database"]["mysql"]);

        switch ($action) {
            case 'semester-courses':
                $semester_data = $semesterObj->currentSemester();
                if (empty($semester_data)) die(json_encode(array("success" => false, "message" => "No courses assigned to your class yet.")));
                die(json_encode(array("success" => false, "message" => $semester_data)));
            default:
                die(json_encode(array("success" => false, "message" => "No match found for your request!")));
        }
    }
}

//
elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    $action = $separatePath[1];

    if ($module === 'student') {
        $studentObj = new Student($config["database"]["mysql"]);

        switch ($action) {

            case 'login':

                if (!isset($_SESSION["_start"]) || empty($_SESSION["_start"]))
                    die(json_encode(array("success" => false, "message" => "Invalid request: 1!")));
                if (!isset($_POST["_logToken"]) || empty($_POST["_logToken"]))
                    die(json_encode(array("success" => false, "message" => "Invalid request: 2!")));
                if ($_POST["_logToken"] !== $_SESSION["_start"])
                    die(json_encode(array("success" => false, "message" => "Invalid request: 3!")));

                $username = Validator::IndexNumber($_POST["usp_identity"]);
                $password = Validator::Password($_POST["usp_password"]);

                $result = $studentObj->login($username, $password);
                if (!$result["success"]) die(json_encode($result));

                $semesterObj = new Semester($config["database"]["mysql"]);
                $semester_data = $semesterObj->currentSemester();
                if (!empty($semester_data)) {
                    $_SESSION["semester"]["id"] = $semester_data["semester_id"];
                    $_SESSION["semester"]["name"] = $semester_data["semester_name"];
                    $_SESSION["semester"]["reg_status"] = $semester_data["reg_open_status"];
                    $_SESSION["semester"]["reg_date"] = $semester_data["reg_end_date"];
                    $_SESSION["semester"]["acad_y_id"] = $semester_data["academic_year_id"];
                    $_SESSION["semester"]["acad_y_name"] = $semester_data["academic_year_name"];
                }

                $_SESSION["student"]['login'] = true;
                $_SESSION["student"]['index_number'] = $result["message"]["index_number"];
                die(json_encode(array("success" => true,  "message" => "Login successfull!")));

            case 'semesterCourses':
                // /$result = $studentObj->assignCourse($_POST);
                $feed = Validator::SendResult($result, $result, "Account not found!");
                die(json_encode($feed));

            default:
                # code...
                break;
        }
    }
}
