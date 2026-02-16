<?php

/**
 * Centralized authentication and session middleware.
 * Include this file at the top of every authenticated page.
 *
 * Sets up: session, auth checks, common data (student, semester).
 *
 * Usage: require_once('inc/auth.php');
 * After include, these variables are available:
 *   $student_index, $student_data, $current_semester,
 *   $semester_label, $student_level, $student_image,
 *   $studentObj, $semesterObj
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../bootstrap.php');

use Src\Core\Base;
use Src\Controller\Semester;
use Src\Controller\Student;

// Session expiry check
if (Base::sessionExpire()) {
    echo "<script>alert('Your session expired, logging you out...');</script>";
    header('Location: login.php');
    exit;
}

// Auth check - must be logged in
if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) {
    header('Location: login.php');
    exit;
}

// Default password check - force password change
if (!empty($_SESSION["student"]['default_password'])) {
    header("Location: create-password.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    Base::logout();
    exit;
}

// Load common data
$config = require(__DIR__ . '/../config/database.php');

$studentObj = new Student($config["database"]["mysql"]);
$semesterObj = new Semester($config["database"]["mysql"]);

$student_index = isset($_SESSION["student"]["index_number"]) && !empty($_SESSION["student"]["index_number"])
    ? $_SESSION["student"]["index_number"]
    : "";

$student_data = $studentObj->fetchData($student_index);
$current_semester = $semesterObj->currentSemester();
$student_level = $studentObj->getCurrentLevel($student_index);
$student_image = 'https://admissions.rmuictonline.com/apply/photos/' . ($student_data["photo"] ?? '');

// Build semester label (1st, 2nd)
$semester_label = '';
if (!empty($current_semester)) {
    $sem_num = $current_semester["semester_name"];
    if ($sem_num == 1) {
        $semester_label = $sem_num . "<sup>st</sup>";
    } elseif ($sem_num == 2) {
        $semester_label = $sem_num . "<sup>nd</sup>";
    }
}
