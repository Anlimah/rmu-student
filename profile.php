<?php
session_start();

require_once('bootstrap.php');

use Src\Core\Base;

//Base::dd($_SESSION);

if (Base::sessionExpire()) {
    echo "<script>alert('Your session expired, logging you out...');</script>";
}

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) header('Location: login.php');
if ($_SESSION["student"]['default_password']) header("Location: create-password.php");

if (isset($_GET['logout'])) Base::logout();

use Src\Controller\Semester;
use Src\Controller\Student;

$config = require('config/database.php');
$student_index = isset($_SESSION["student"]['index_number']) && !empty($_SESSION["student"]["index_number"]) ? $_SESSION["student"]["index_number"] : "";

$studentObj = new Student($config["database"]["mysql"], "mysql", getenv('TEST_DB_ADMISSION_USERNAME'), getenv('TEST_DB_ADMISSION_PASSWORD'));
$student_data = $studentObj->fetchData($student_index);

$semster = new Semester($config["database"]["mysql"], "mysql", getenv('TEST_DB_ADMISSION_USERNAME'), getenv('TEST_DB_ADMISSION_PASSWORD'));
$current_semester = $semster->currentSemester();

if (!empty($current_semester)) {
    if ($current_semester["semester_name"] == 1)
        $semester = $current_semester["semester_name"] . "<sup>st</sup>";
    elseif ($current_semester["semester_name"] == 2)
        $semester = $current_semester["semester_name"] . "<sup>nd</sup>";
}

$student_level = 100;
$student_image = 'https://admissions.rmuictonline.com/apply/photos/' . $student_data["photo"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Home</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <?php require_once("inc/apply-head-section.php") ?>

    <style>
        .item-card {
            display: flex;
            align-items: center;
            height: 80px;
            padding: 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .item-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .item-card img {
            width: 50px;
            height: 50px;
            margin-right: 20px;
        }

        .item-card p {
            color: #003262;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .arrow-link {
            margin-left: auto;
            color: #003262;
            text-decoration: none;
            padding-left: 10px;
            font-size: 18px;
        }

        .transform-text {
            text-transform: uppercase !important;
        }

        .profile-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            background-color: #003262 !important;
            border-radius: 5px !important;
            border-color: transparent !important;
            padding: 15px 15px !important;
        }
    </style>
</head>

<body id="body">

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main class="container">

            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href=""><?= "from" ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Student Profile</li>
                </ol>
            </nav>

            <div class="row mb-4">
                <div class="col-xxl-12 col-md-12">
                    <div class="profile-card">
                        <div class="student-img" style="text-align: center; padding: 5px;">
                            <img src="<?= $student_image ?>" alt="<?= $student_data["full_name"] ?>" style="border-radius: 50%; border: 2px solid white; width: 100px; height: 100px;">
                        </div>

                        <div class="student-name " style="text-align: center; color: #FFA000; font-weight: 600">
                            <?= $student_data["full_name"] ?>
                        </div>

                        <div class="student-index" style="text-align: center; color: white; font-weight: 600">
                            <?= $student_index ?>
                        </div>

                        <div style="display: flex; justify-content: center; align-items:center; margin-top: 10px; ">
                            <div class="student-program me-2 " style="color: white; font-weight: 600">
                                <?= $student_data["program_name"] ?>
                            </div>
                            <div class="student-program " style="color: #FFA000; font-weight: 600">
                                [<?= $student_data["class_code"] ?>]
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>

        <!-- footer -->

        <?php require_once("inc/app-sections-menu.php"); ?>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/myjs.js"></script>
    <script>
    </script>
</body>

</html>