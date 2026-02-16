<?php
session_start();

if (isset($_SESSION["student"]["login"]) && $_SESSION["student"]["login"] === true) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION["_start"])) {
    $rstrong = true;
    $_SESSION["_start"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
}
$_SESSION["lastAccessed"] = time();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Portal | Sign In</title>
    <?php require_once("inc/head.php") ?>
</head>

<body>
    <div class="login-page">

        <div class="login-page__header">
            <div class="login-page__brand">
                <img src="assets/images/rmu-logo-small.png" alt="RMU Logo" class="login-page__logo">
                <div>
                    <div class="login-page__title">REGIONAL MARITIME UNIVERSITY</div>
                    <div class="login-page__subtitle">Student Portal</div>
                </div>
            </div>
        </div>

        <main class="login-page__body">
            <div class="login-card">
                <img src="assets/images/icons8-id-verified-96.png" alt="Sign In" class="login-card__icon">
                <h1 class="login-card__heading">Sign In</h1>

                <div class="login-card__divider"></div>

                <div id="loginMsgDisplay"></div>

                <form id="appLoginForm">
                    <div class="form-group">
                        <input class="form-input form-input--underline" type="text" id="usp_identity" name="usp_identity" placeholder="Index Number" autocomplete="username">
                    </div>

                    <div class="form-group">
                        <input class="form-input form-input--underline" type="password" id="usp_password" name="usp_password" placeholder="Password" autocomplete="current-password">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary btn--block" id="loginSubmitBtn">Sign In</button>
                    </div>

                    <input type="hidden" name="_logToken" value="<?= $_SESSION['_start'] ?>">
                </form>

                <a href="forgot-password.php" class="login-card__link">Forgot your password?</a>
            </div>
        </main>

        <footer class="app__footer">
            &copy; <?= date('Y') ?> Regional Maritime University. All rights reserved.
        </footer>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/portal.js"></script>
    <script>
        $(document).ready(function() {
            $("#appLoginForm").on("submit", function(e) {
                e.preventDefault();
                var btn = $("#loginSubmitBtn");
                btn.prop("disabled", true).text("Signing in...");

                $.ajax({
                    type: "POST",
                    url: "api/student/login",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        if (result.success) {
                            showAlert('loginMsgDisplay', 'success', '<strong>' + result.message + '</strong>');
                            setTimeout(function() {
                                showAlert('loginMsgDisplay', 'success', '<strong>Redirecting...</strong>');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            }, 1500);
                            return;
                        }
                        btn.prop("disabled", false).text("Sign In");
                        showAlert('loginMsgDisplay', 'danger', '<strong>' + result.message + '</strong>');
                    },
                    error: function(xhr) {
                        btn.prop("disabled", false).text("Sign In");
                        showAlert('loginMsgDisplay', 'danger', '<strong>An error occurred. Please try again.</strong>');
                    }
                });
            });
        });
    </script>
</body>

</html>
