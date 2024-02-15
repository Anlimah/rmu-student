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

$student = new Student($config);

$data = [];
$errors = [];

// All GET request will be sent here
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($_GET["url"] == "programs") {
        if (isset($_GET["type"])) {
            $t = 0;
            if ($_GET["type"] != "All") {
                $t = (int) $_GET["type"];
            }
            $result = $admin->fetchPrograms($t);
            if (!empty($result)) {
                $data["success"] = true;
                $data["message"] = $result;
            } else {
                $data["success"] = false;
                $data["message"] = "No result found!";
            }
        }
        die(json_encode($data));
    } elseif ($_GET["url"] == "form-price") {
        if (!isset($_GET["form_key"]) || empty($_GET["form_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchFormPrice($_GET["form_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching form price details!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "vendor-form") {
        if (!isset($_GET["vendor_key"]) || empty($_GET["vendor_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchVendor($_GET["vendor_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching vendor details!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "prog-form") {
        if (!isset($_GET["prog_key"]) || empty($_GET["prog_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchProgramme($_GET["prog_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching programme information!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "adp-form") {
        if (!isset($_GET["adp_key"]) || empty($_GET["adp_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchAdmissionPeriodByID($_GET["adp_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching admissions information!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "user-form") {
        if (!isset($_GET["user_key"]) || empty($_GET["user_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchSystemUser($_GET["user_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching user account information!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "programsByCategory") {
        if (!isset($_GET["cert-type"]) || empty($_GET["cert-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchAllFromProgramByCode($_GET["cert-type"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Failed to fetch programs for this certificate category [{$_GET["cert-type"]}]!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }

    // All POST request will be sent here
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {

    if ($_GET["url"] == "studentLogin") {

        if (!isset($_SESSION["_start"]) || empty($_SESSION["_start"]))
            die(json_encode(array("success" => false, "message" => "Invalid request: 1!")));
        if (!isset($_POST["_logToken"]) || empty($_POST["_logToken"]))
            die(json_encode(array("success" => false, "message" => "Invalid request: 2!")));
        if ($_POST["_logToken"] !== $_SESSION["_start"])
            die(json_encode(array("success" => false, "message" => "Invalid request: 3!")));

        $username = Validator::IndexNumber($_POST["usp_identity"]);
        $password = Validator::Password($_POST["usp_password"]);

        $result = $student->login($username, $password);
        if (!$result) die(json_encode(array("success" => false, "message" => "Incorrect index number or password! ")));

        $_SESSION['login'] = true;
        $_SESSION['index_number'] = $result["index_number"];
        die(json_encode(array("success" => true,  "message" => strtolower($result["role"]))));
    }
}
