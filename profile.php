<?php
include_once("rest.php");
include_once("lib.php");
include_once("api_lib.php");

$db = connect_to_db();
$username = login_get();

// If there is a requested ID load that
if (array_key_exists("id", $_GET))
{
    $user_id = $_GET["id"];
}
// If not, load the logged in user's profile
elseif (user_exists($db, $username))
{
    $user_id = get_user_id($db, $username);
}
// Otherwise don't load anything
else
{
    $user_id = 0;
}

if (array_key_exists("username", $_GET))
{
    $requested_username = $_GET["username"];
    $user_id = get_user_id($db, $requested_username);
}
else
{
    $requested_username = get_username($db, $user_id);
}

$logged_in = user_exists($db, $username);
?>

<!DOCTYPE html>
<html>

<head>
    <script src="assets/js/jquery.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Photography Blog Profile</title>
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

<body>

    <!-- NAV -->
    <nav class="navbar navbar-dark navbar-expand-md" id="app-navbar">
        <div class="container-fluid"><a class="navbar-brand" href="/blog/"><i class="fas fa-camera" id="brand-logo"></i></a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="/blog/" style="font-family: Allerta, sans-serif;">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#" style="font-family: Allerta, sans-serif;">Profile</a></li>
                    <?php if ($logged_in) { ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php" style="font-family: Allerta, sans-serif;">Logout</a></li>
                    <?php } else {?>
                        <li class="nav-item"><a class="nav-link" href="login.php" style="font-family: Allerta, sans-serif;">Login</a></li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </nav>


<!-- Handle invalid search request -->
<?php if (!user_exists($db, $requested_username)) { ?>
    <script>

        alert("Invalid username");
        window.location.replace("/blog/");

    </script>
<?php } ?>

<!-- Handle not logged in -->
<?php if ($user_id == 0) { ?>
    <script>

        alert("You must login to access your profile");
        window.location.replace("login.php");

    </script>
<?php } ?>


    <!-- POSTS BY USER -->
    <div class="container" id="profile-container">
        <h1>Posts by <?= $requested_username ?></h1>
        <hr>

<?php
    $req = ["username" => $requested_username];
    $allPosts = json_decode(get_posts($db, $req));

    foreach($allPosts as $post)
    {
        $post_id = $post -> id;
        $user_id = $post -> user_id;
        $post_text = $post -> post_text;
        $extra = json_decode($post -> extra);
        $post_title = $extra -> post_title;
        $image = substr(($extra -> image), 6);  // 6 is the start index of the image url
        $post_username = get_username($db, $user_id);
?>
    <a href="blog_post.php?id=<?= $post_id ?>" class="post-sample-link">
        <div class="media post-sample"><img class="mr-3 post-sample-img" src="<?= $image ?>" />
            <div class="media-body">
            <h5 class="post-sample-title"><?= $post_title ?></h5>
            <h6 class="post-sample-author">By <em class="post-sample-username"><?= $post_username ?></em></h6>
            <p class="post-sample-body"><?= $post_text ?> </p>
            <hr />
        </div>
    </div>
    </a>
<?php } ?>
</div>

<!-- SEARCH BUTTON -->
<div class="dashed_upload"></div>
<a href="#" class="float" id="profile-search-button" data-toggle="tooltip" data-placement="left" title="Search">
<i class="fas fa-search my-float" style="color: rgb(255,255,255)"></i>
</a>

<!-- SEARCH MODAL -->
    <div class="modal fade .modal-dialog-centered" role="dialog" tabindex="-1" id="search-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Search Posts By User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form id="search-form-2" action="profile.php" method="get">
                        <div class="form-group">
                            <label for="new-post-title">Enter Exact Username 😉</label>
                            <input class="form-control" type="text" id="search-username-1" name="username" required="">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light btn-lg" type="button" data-dismiss="modal">Close</button>
                    <button id="nice-button" class="btn btn-primary btn-lg btn-warning search-button" type="submit" form="search-form-2" style="background: var(--warning);">Search&nbsp;<i class="fa fa-search"></i></button>
                </div>

                <div class="modal fade .modal-dialog-centered" role="dialog" tabindex="-1" id="new-post-modal-1">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Create New Post</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="form-group"><label for="new-post-title">Title</label><input class="form-control" type="text" id="new-post-title-1" required=""></div>
                                    <div class="form-group"><label for="new-post-body">Body</label><textarea class="form-control form-control-lg" id="new-post-body-1" required=""></textarea></div>
                                    <div class="form-group files color">
                                        <label>Image</label>
                                        <input class="form-control-file" type="file" id="new-post-file-input-1" name="files" required="">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"><button class="btn btn-light btn-lg" type="button" data-dismiss="modal">Close</button><button class="btn btn-primary btn-lg" type="submit">Post&nbsp;<i class="fa fa-plus-circle"></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/blog.js"></script>
    <script src="assets/js/Swiper-Slider-Card-For-Blog-Or-Product.js"></script>
</body>

</html>