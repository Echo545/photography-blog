<?php
include_once("rest.php");
include_once("lib.php");
include_once("api_lib.php");

$db = connect_to_db();
$username = login_get();

$logged_in = user_exists($db, $username);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Photography Blog Login</title>
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Allerta">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Averia+Sans+Libre">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="assets/css/Drag--Drop-Upload-Form.css">
    <link rel="stylesheet" href="assets/css/Drag-Drop-File-Input-Upload.css">
    <link rel="stylesheet" href="assets/css/Floating-Button.css">
    <link rel="stylesheet" href="assets/css/gradient-navbar.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/Landing-Page---Parallax-Background---Logo-Heading-ButtonGIF.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/post.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/Swiper-Slider-Card-For-Blog-Or-Product-1.css">
    <link rel="stylesheet" href="assets/css/Swiper-Slider-Card-For-Blog-Or-Product.css">
</head>

<body id="login-body">
    <nav class="navbar navbar-dark navbar-expand-md" id="app-navbar">
        <div class="container-fluid"><a class="navbar-brand" href="/blog/"><i class="fas fa-camera" id="brand-logo"></i></a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="/blog/" style="font-family: Allerta, sans-serif;">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php" style="font-family: Allerta, sans-serif;">Profile</a></li>

                    <?php if ($logged_in) { ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php" style="font-family: Allerta, sans-serif;">Logout</a></li>
                    <?php } else {?>
                        <li class="nav-item"><a class="nav-link active" href="login.php" style="font-family: Allerta, sans-serif;">Login</a></li>
                    <?php }?>

                </ul>
            </div>
        </div>
    </nav>
    <div class="login-clean">
        <form method="post" action="login.php" id="login-form">
            <h2 class="sr-only">Login Form</h2>
            <div class="illustration"><i class="fas fa-camera"></i>
        </div>
            <div class="form-group">
                <h1 id="login-head">LOGIN</h1>
                <hr id="pink-hr">
                <input class="form-control" type="text" name="username" placeholder="Username" required="" id="login-username-field" minlength="2" maxlength="20">
            </div>
            <div class="form-group">
                <input class="form-control" type="password" name="password" placeholder="Password" required="" id="login-password-field">
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block" onclick="submitLogin()">Log In</button>
            </div>
            <a class="forgot" href="register.php"><h6>Don't have an account? <br> Register here</h6></a>
        </form>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/blog.js"></script>
    <script src="assets/js/Swiper-Slider-Card-For-Blog-Or-Product.js"></script>
</body>

</html>