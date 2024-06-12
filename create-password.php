<?php
session_start();

require_once('bootstrap.php');

use Src\Core\Base;

// Base::dd($_SESSION);/

if (Base::sessionExpire());

if (!isset($_SESSION["student"]['login']) || $_SESSION["student"]['login'] !== true) header('Location: login.php');

if (isset($_GET['logout'])) Base::logout();

if (!isset($_SESSION["_start_create_password"])) {
    $rstrong = true;
    $_SESSION["_start_create_password"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>RMU Online Applicatioin Portal</title>
    <?php require_once("inc/apply-head-section.php") ?>

    <style>
        .form-control-login {
            padding: 12px !important;
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            border-bottom: 1px solid #003262 !important;
            border-radius: 0 !important;
            font-size: 16px !important;
            color: #003262 !important;
            font-weight: 550 !important;
        }

        .form-control-login:focus {
            color: #212529;
            background-color: #fff;
            outline: 0;
            box-shadow: none !important;
            border-color: #86b7fe transparent !important;
        }

        @media (width < 769px) {
            .loginFormContainer {
                margin-top: 70px;
            }
        }
    </style>
</head>

<body>

    <div id="wrapper">

        <?php require_once("inc/page-nav2.php") ?>

        <main>
            <div class="row">

                <div class="col login-section">
                    <section class="login">

                        <div style="width:auto;">

                            <!--Form card-->
                            <div class="card loginFormContainer" style="margin-bottom: 10px; min-width: 360px; max-width: 360px;">
                                <div class="row" style="display: flex; justify-content:center; padding-top: 50px;  padding-bottom: 0;  margin-bottom:0 !important;">
                                    <img src="assets/images/icons8-security-user-male-96.png" alt="sign in image" style="width: 120px;">
                                    <h1 class="text-center" style="color: #003262; margin: 15px 0px !important;">Create New Password</h1>
                                </div>

                                <hr style="padding-top: 15px !important;">

                                <div style="margin: 0px 12% !important">
                                    <div id="loginMsgDisplay"> </div>
                                    <form id="appLoginForm">
                                        <div class="mb-4">
                                            <input type="password" id="new-usp-password" name="new-usp-password" class="form-control form-control-lg form-control-login" placeholder="New Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*['@,$,#,!,*,+,\-.,\\])(?=.*\d).{8,16}$" title="Password must be at least 8 and most 16 characters long and contain at least one uppercase letter, one lowercase letter, one digit, and one special character">
                                        </div>
                                        <div class="mb-4">
                                            <input type="password" id="re-usp-password" class="form-control form-control-lg form-control-login" placeholder="Retype Password">
                                        </div>
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary form-btn-login">Create New Password</button>
                                        </div>
                                        <input type="hidden" name="_cpToken" value="<?= $_SESSION['_start_create_password'] ?>">
                                    </form>


                                </div>

                            </div>

                        </div>

                    </section>

                </div>

            </div>

        </main>

    </div>


    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            if (window.location.href == "https://admissions.rmuictonline.com/apply/index.php" || window.location.href == "https://admissions.rmuictonline.com/apply/") {
                $(".sign-out-1").hide();
            }

            if (window.location.href == "http://localhost/rmu-student/login.php") {
                $(".sign-out-1").hide();
            }

            $(document).on("click", ".logout-btn", function() {
                window.location.href = "?logout";
            });

            //$("#usp_identity").focus();

            $("#appLoginForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "api/student/create-password",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (!result.success && result.message == "logout") {
                            alert(result.message);
                            window.location.href = "?logout=true";
                            return;
                        } else {
                            if (result.success) {
                                $("#loginMsgDisplay").html('<div class="alert alert-success" role="alert">' + result.message + '</div>');

                                setTimeout(function() {
                                    payload = result.data;
                                    $("#loginMsgDisplay").html('<div class="alert alert-success" role="alert">Setting up account...</div>');

                                    $.ajax({
                                        type: "POST",
                                        url: "api/student/setup-account",
                                        data: payload,
                                        success: function(result) {
                                            console.log(result);
                                            if (!result.success && result.message == "logout") {
                                                window.location.href = "?logout=true";
                                                return;
                                            } else {
                                                setTimeout(function() {
                                                    if (result.success) {
                                                        $("#loginMsgDisplay").html('<div class="alert alert-success" role="alert">' + result.message + '</div>');
                                                    } else {
                                                        $("#loginMsgDisplay").html('<div class="alert alert-danger" role="alert">' + result.message + '</div>');
                                                    }
                                                    setTimeout(function() {
                                                        window.location.href = "index.php";
                                                    }, 1000);
                                                }, 1000);
                                            }
                                        },
                                        error: function(error) {
                                            console.log(error);
                                        }
                                    });
                                }, 2000);
                            } else {
                                $("#loginMsgDisplay").html('<div class="alert alert-danger" role="alert" style="font-weight: bold">' + result.message + '</div>');
                            }
                            $("#enrollAppBtn-text").text("Enroll");
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 401) {
                            alert("Your session expired, logging you out...");
                            window.location.href = "?logout";
                        } else {
                            console.log("Error: " + status + " - " + error);
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>