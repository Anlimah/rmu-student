<?php
session_start();

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

use Src\Controller\Course;
use Src\Core\Base;

if (Base::sessionExpire()) {
    http_response_code(401);
    die(json_encode(array("success" => false, "message" => "logout")));
}

$config = require('../config/database.php');
$db_server = "mysql";
$db_username = isset($_SESSION["connection"]["username"]) && !empty($_SESSION["connection"]["username"]) ? $_SESSION["connection"]["username"] : getenv('LOCAL_DB_ADMISSION_USERNAME');
$db_password = isset($_SESSION["connection"]["password"]) && !empty($_SESSION["connection"]["password"]) ? $_SESSION["connection"]["password"] : getenv('LOCAL_DB_ADMISSION_PASSWORD');

use Src\Controller\Student;
use Src\Core\Validator;
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
        $studentObj = new Student($config["database"][$db_server], $db_server, $db_username, $db_password);

        switch ($action) {
            case 'semester-courses':
                $st_semester_courses = $studentObj->fetchSemesterCourses(
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"]
                );
                if (empty($st_semester_courses)) {
                    die(json_encode(array("success" => false, "message" => "No courses assigned to you yet.")));
                }
                die(json_encode(array("success" => true, "message" => $st_semester_courses)));

            case 'registration-summary':
                $result = $studentObj->fetchCourseRegistrationSummary(
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"]
                );
                $feed = Validator::SendResult($result, $result, $result);
                die(json_encode($feed));

                // gets all the assigned semester courses 
            case 'other-semester-courses':
                //die(json_encode($_SESSION["semester"]["name"]));
                $st_semester_courses = $studentObj->fetchCoursesBySemAndLevel(
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"],
                    $_SESSION["semester"]["name"],
                    200
                );
                if (empty($st_semester_courses)) {
                    die(json_encode(array("success" => false, "message" => "You don't have unregistered courses.")));
                }
                die(json_encode(array("success" => true, "message" => $st_semester_courses)));
            default:
                die(json_encode(array("success" => false, "message" => "No match found for your request!")));
        }
    } else if ($module === 'course') {
        $courseObj = new Course($config["database"]["mysql"]);

        switch ($action) {
            case 'info':
                if (!isset($_GET["cc"]) || empty($_GET["cc"])) {
                    die(json_encode(array("success" => false, "message" => "Invalid request!")));
                }
                $course_info = $courseObj->courseInfo($_GET["cc"]);
                if (empty($course_info)) {
                    die(json_encode(array("success" => false, "message" => "No results found for this course!")));
                }
                die(json_encode(array("success" => true, "message" => $course_info)));
                break;

            default:
                # code...
                break;
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
                $_SESSION["student"]['default_password'] = $result["message"]["default_password"];
                die(json_encode(array("success" => true,  "message" => "Login successfull!")));

            case 'create-password':
                if (!isset($_SESSION["_start_create_password"]) || empty($_SESSION["_start_create_password"]))
                    die(json_encode(array("success" => false, "message" => "Invalid request: 1!")));
                if (!isset($_POST["_cpToken"]) || empty($_POST["_cpToken"]))
                    die(json_encode(array("success" => false, "message" => "Invalid request: 2!")));
                if ($_POST["_cpToken"] !== $_SESSION["_start_create_password"])
                    die(json_encode(array("success" => false, "message" => "Invalid request: 3!")));

                $password = Validator::Password2($_POST["new-usp-password"]);
                $result = $studentObj->createNewPassword($_SESSION["student"]["index_number"], $password);
                if (!$result["success"]) die(json_encode($result));
                $_SESSION["student"]['default_password'] = 0;
                die(json_encode($result));

                // gets all the assigned semester courses 
            case 'semester-courses':
                $st_semester_courses = $studentObj->fetchSemesterCourses(
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"]
                );
                if (empty($st_semester_courses)) {
                    die(json_encode(array("success" => false, "message" => "No courses assigned to you yet.")));
                }
                die(json_encode(array("success" => true, "message" => $st_semester_courses)));

            case 'register-courses':
                if (!isset($_POST['selected-course']) || !is_array($_POST['selected-course'])) {
                    die(json_encode(array("success" => false,  "message" => "You have not selected any course!")));
                }
                $selected_courses = Validator::InputTextNumberForArray($_POST['selected-course']);
                $result = $studentObj->registerSemesterCourses(
                    $selected_courses,
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"]
                );
                $feed = Validator::SendResult(
                    $result,
                    "You have successfully registered $result courses for the semester!",
                    "Failed to register your semester courses. The process could not complete!"
                );
                die(json_encode($feed));


            case 'reset-course-registration':
                $result = $studentObj->resetCourseRegistration(
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"]
                );
                $feed = Validator::SendResult(
                    $result,
                    "Semester course registration reseted!",
                    "Failed to reset semester courses registration!"
                );
                die(json_encode($feed));

                // gets all the assigned semester courses 
            case 'add-course-to-register':
                $st_semester_courses = $studentObj->fetchCoursesBySemAndLevel(
                    $_SESSION["student"]["index_number"],
                    $_SESSION["semester"]["id"],
                    $_SESSION["semester"]["name"],
                    200
                );
                if (empty($st_semester_courses)) {
                    die(json_encode(array("success" => false, "message" => "You don't have unregistered courses.")));
                }
                die(json_encode(array("success" => true, "message" => $st_semester_courses)));

            default:
                # code...
                break;
        }
    }
}
