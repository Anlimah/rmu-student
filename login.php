<?php
session_start();

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) header("Location: index.php");

if (!isset($_SESSION["_start"])) {
    $rstrong = true;
    $_SESSION["_start"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
$_SESSION["lastAccessed"] = time();
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

                        <div style="width:auto">

                            <!--Form card-->
                            <div class="card loginFormContainer" style="margin-bottom: 10px; min-width: 360px; max-width: 360px">
                                <div class="row" style="display: flex; justify-content:center; padding-top: 50px;  padding-bottom: 0;  margin-bottom:0 !important;">
                                    <img src="assets/images/icons8-mobile-id-verification-100.png" alt="sign in image" style="width: 120px;">
                                    <h1 class="text-center" style="color: #003262; margin: 15px 0px !important;">Sign In</h1>
                                </div>

                                <hr style="padding-top: 15px !important;">

                                <div style="margin: 0px 12% !important">
                                    <form id="appLoginForm">
                                        <div class="mb-4">
                                            <input class="form-control form-control-lg form-control-login" type="text" id="usp_identity" name="usp_identity" placeholder="Email or Index Number">
                                        </div>

                                        <div class="mb-4">
                                            <input class="form-control form-control-lg form-control-login" type="password" id="usp_password" name="usp_password" placeholder="Password">
                                        </div>

                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary form-btn-login">Login</button>
                                        </div>

                                        <input type="hidden" name="_logToken" value="<?= $_SESSION['_start'] ?>">
                                    </form>

                                    <div class="row" style="margin-bottom:30px;">
                                        <a href="reset-password.php" style="color: #003262 !important;">Forgot your password?</a>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>

                </div>

            </div>

        </main>
        <?php require_once("inc/page-footer.php"); ?>

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

            //$("#usp_identity").focus();

            $("#appLoginForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "api/studentLogin",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        alert(result['message']);
                        if (result.success) window.location.reload();
                    },
                    error: function(error) {}
                });
            });
        });
    </script>
</body>

</html>