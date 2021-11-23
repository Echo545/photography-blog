<?php
include_once("rest.php");
include_once("lib.php");
include_once("api_lib.php");

$db = connect_to_db();
$username = login_get();


$post_id = 2;   // default post

// Read post ID
if (array_key_exists("id", $_GET))
{
    $post_id = $_GET["id"];
}

$post = get_post_from_id($db, $post_id);
$post_user_id = $post["user_id"];
$post_text = $post["post_text"];
$extra = json_decode($post["extra"]);
$post_title = $extra -> post_title;
$image = substr(($extra -> image), 6);  // 6 is the start index of the image url
$post_username = get_username($db, $post_user_id);

$logged_in = user_exists($db, $username);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Photography Blog</title>
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Allerta">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Averia+Sans+Libre">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="assets/css/Floating-Button.css">
    <link rel="stylesheet" href="assets/css/gradient-navbar.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/Landing-Page---Parallax-Background---Logo-Heading-ButtonGIF.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/post.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<!-- NAV -->
<body id="post-body">
    <nav class="navbar navbar-dark navbar-expand-md" id="app-navbar">
        <div class="container-fluid"><a class="navbar-brand" href="/blog/"><i class="fas fa-camera" id="brand-logo"></i></a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="/blog/" style="font-family: Allerta, sans-serif;">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php" style="font-family: Allerta, sans-serif;">Profile</a></li>
                    <?php if ($logged_in) { ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php" style="font-family: Allerta, sans-serif;">Logout</a></li>
                    <?php } else {?>
                        <li class="nav-item"><a class="nav-link" href="login.php" style="font-family: Allerta, sans-serif;">Login</a></li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- POST CONTENT -->
    <img class="shadow-sm img-fluid rounded mx-auto" id="post-image" src="<?= $image ?>">
    <div class="container" id="post-container">
        <h1 id="post-title"><?= $post_title ?></h1>
        <h3 style="text-align: center;"><a id="post-username" href="profile.php?id=<?= $post_user_id ?>"><?= $post_username ?></a></h3>
        <hr>
        <div id="post-body-container" class="container rounded">
            <p id="post-body" style="text-align: left;font-size: 18px;"><?= $post_text ?><br></p>
        </div>
    </div>

<!-- COMMENTS -->
<div class="container" id="comments-container">
    <h2 style="text-align: center;">Comments</h2>
    <hr>

<!-- COMMENTS -->
<?php
    $comments = json_decode(get_comments($db, ["post_id" => $post_id]));

    // Default value
    $comment_id = 0;

    foreach($comments as $comment)
    {
        $comment_user_id = $comment -> user_id;
        $comment_id = $comment -> id;
        $comment_text = $comment -> comment_text;
        $commenter_username = get_username($db, $comment_user_id);
?>
        <div class="shadow-sm comment-template rounded">
            <h5>By&nbsp;<a class="comment-username" href="profile.php?id=<?= $comment_user_id ?>"><?= $commenter_username ?></a></h5>
            <p class="comment-body">

                <span id="comment-body-<?= $comment_id ?>"><?= $comment_text ?></span>

            <?php if ($logged_in && $comment_user_id == get_user_id($db, $username)) { ?>
                <!-- DELETE COMMENT BUTTON -->
                <a class="comment-delete-button" href="#" onclick="deleteComment(<?= $comment_id ?>);" data-toggle="tooltip" data-placement="right" title="Delete Comment">
                    <i class="fas fa-trash my-float" style="color: rgb(255,255,255)" id="comment-trash-icon"></i>
                </a>

                <!-- EDIT COMMENT BUTTON -->
                <a class="comment-edit-button" href="#" onclick="updateCommentEditID(<?= $comment_id ?>); document.getElementById('edit-comment-body').value = document.getElementById('comment-body-' + <?= $comment_id ?>).innerHTML;" data-toggle="tooltip" data-placement="right" title="Edit Comment">
                    <i class="fas fa-pencil-alt my-float" style="color: rgb(255,255,255)" id="comment-edit-icon"></i>
                </a>

            <?php } ?>

            <br></p>
        </div>
<?php } ?>
    </div>

<!-- NEW COMMENT -->
    <div class="container" id="new-comment-container">
        <form id="new-comment-form">
            <div class="form-group">
            <label for="comment-textarea"><h5>New Comment</h5></label>


            <?php if (user_exists($db, $username)) { ?>
                <textarea class="form-control form-control-lg" id="comment-textarea" required="" minlength="4"></textarea>
                </div>
                <button onclick="newComment(<?= $post_id ?>)" class="btn btn-primary btn-lg" type="submit">Submit</button>
            <?php } else { ?>
                <textarea class="form-control form-control-lg" id="comment-textarea" required="" minlength="4" disabled>You must be logged in to make a comment.</textarea>
                </div>
                <button class="btn btn-primary btn-lg" type="button" disabled>Submit</button>
            <?php } ?>

        </form>
    </div>

<?php
    $poster_username = get_username($db, get_user_id_from_post($db, $post_id));

// Verify that the current user is the same who made the post
if (user_logged_in($poster_username)) { ?>

    <!-- DELETE POST BUTTON -->
    <a href="#" onclick="deletePost(<?= $post_id ?>);" class="float" id="delete-post-button" data-toggle="tooltip" data-placement="left" title="Delete Post">
        <i class="fas fa-trash my-float" style="color: rgb(255,255,255)"></i>
    </a>

    <!-- EDIT POST BUTTON -->
    <a href="#" class="float" id="edit-post-button" data-toggle="tooltip" data-placement="left" title="Edit Post">
        <i class="fas fa-pencil-alt my-float" style="color: rgb(255,255,255)"></i>
    </a>

    <!-- EDIT POST MODAL -->
    <div class="modal fade .modal-dialog-centered" role="dialog" tabindex="-1" id="edit-post-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Post</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="edit-post-form">
                        <div class="form-group">
                            <label for="edit-post-title">Title</label>
                            <input class="form-control" type="text" id="edit-post-title" required="">
                        </div>
                        <div class="form-group">
                            <label for="edit-post-body">Body</label>
                            <textarea class="form-control form-control-lg" id="edit-post-body" required=""></textarea>
                        </div>
                    </form>
                </div>

                <!-- Fill in default values -->
                <script>
                    document.getElementById("edit-post-title").value = "<?= $post_title ?>";
                    document.getElementById("edit-post-body").value = "<?= $post_text ?>";
                </script>


                <div class="modal-footer">
                    <button class="btn btn-light btn-lg" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary btn-lg" onclick="updatePost(<?= $post_id ?>)">Edit&nbsp;<i class="fas fa-pencil-alt"></i></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

    <!-- EDIT COMMENT MODAL -->
    <div class="modal fade .modal-dialog-centered" role="dialog" tabindex="-1" id="edit-comment-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Comment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="edit-comment-form">
                        <div class="form-group">
                            <label for="edit-comment-body">Comment Body</label>
                            <textarea class="form-control form-control-lg" id="edit-comment-body" required=""></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light btn-lg" type="button" data-dismiss="modal">Close</button>

                    <button class="btn btn-primary btn-lg" onclick="updateComment();">Edit&nbsp;<i class="fas fa-pencil-alt"></i></button>
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