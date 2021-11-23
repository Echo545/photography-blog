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
    <title>Photography Blog</title>
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
    <nav class="navbar navbar-dark navbar-expand-md" id="app-navbar">
        <div class="container-fluid"><a class="navbar-brand" href="/blog/"><i class="fas fa-camera" id="brand-logo"></i></a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link active" href="/blog/" style="font-family: Allerta, sans-serif;">Home</a></li>
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
    <div class="container">
        <div id="home-heading">
            <h1 style="margin-top: 20px;">PHOTOGRAPHY BLOG&nbsp;<i class="fas fa-camera" id="brand-logo-1"></i></h1>
            <hr class="my-hr">

<!-- Featured slider (hardcoded) -->
            <div class="blog-slider">
                <div class="blog-slider__wrp swiper-wrapper">
                    <div class="blog-slider__item swiper-slide">
                        <div></div>
                        <div class="blog-slider__img"><img src="images/9.JPG"></div>
                        <div class="blog-slider__content"><span class="blog-slider__code">26 December 2019</span>
                        <div class="blog-slider__title">Sunset in Kona</div>
                        <div class="blog-slider__text">This was a couple years ago in downtown Kona at Honl's beach. This was back when the volcano was spitting out lots of vog which made the sunsets look fun </div>
                        <a class="class=&quot;blog-slider__button" href="blog_post.php?id=9">READ MORE</a></div>
                    </div>
                    <div class="blog-slider__item swiper-slide">
                        <div></div>
                        <div class="blog-slider__img"><img src="images/8.JPG"></div>
                        <div class="blog-slider__content"><span class="blog-slider__code">26 April 2021</span>
                        <div class="blog-slider__title">End of the World</div>
                        <div class="blog-slider__text">One of my favorite shots from the End of The World in Kona, Hawaii. The surf was really high this day and there was some great sunset lighting. </div>
                        <a class="class=&quot;blog-slider__button" href="blog_post.php?id=8">READ MORE</a></div>
                    </div>
                    <div class="blog-slider__item swiper-slide">
                    <div></div>
                        <div class="blog-slider__img"><img src="images/3.jpg"></div>
                        <div class="blog-slider__content"><span class="blog-slider__code">20 April 2021</span>
                        <div class="blog-slider__title">Big Boat Goes brrr</div>
                        <div class="blog-slider__text">Checkout my super cool picture of this cruise ship coming in on a rainy day off the coast of Hawaii! I took this bad boy years ago from my lanai. Also I made this post using the API in postman and then edited it in the browser, pretty sick! </div>
                        <a class="class=&quot;blog-slider__button" href="blog_post.php?id=7">READ MORE</a></div>
                    </div>
                    <div class="blog-slider__pagination"></div>
                </div>
            </div>
        </div>
        <div id="recent-posts">
            <h2 class="my-h2">ALL&nbsp;POSTS</h2>
            <hr class="my-hr">

<?php
    $req = [];
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
</div>

<!-- SEARCH BUTTON -->
<a href="#" class="float" id="search-button" data-toggle="tooltip" data-placement="left" title="Search">
<i class="fas fa-search my-float" style="color: rgb(255,255,255)"></i>
</a>

<?php
if (user_exists($db, $username)) {
?>
<!-- POST BUTTON -->
<a href="#" class="float" id="new-post-button" data-toggle="tooltip" data-placement="left" title="New Post">
<i class="fa fa-plus my-float" style="color: rgb(255,255,255)" id="new-post-button"></i>
</a>

<!-- POST MODAL -->
    <div class="modal fade .modal-dialog-centered" role="dialog" tabindex="-1" id="new-post-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create New Post</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>

                <div class="modal-body">
                    <form id="new-post-form">
                        <div class="form-group">
                            <label for="new-post-title">Title</label>
                            <input class="form-control" type="text" id="new-post-title" required="">
                        </div>
                        <div class="form-group">
                            <label for="new-post-body">Body</label>
                            <textarea class="form-control form-control-lg" id="new-post-body" required=""></textarea>
                        </div>
                        <div class="form-group files color">
                            <label>Image</label>
                            <input class="form-control-file" type="file" id="new-post-file-input" name="files" required="" accept="image/*">
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light btn-lg" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary btn-lg" onclick="newPost()">Post&nbsp;<i class="fa fa-plus-circle"></i></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
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
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/blog.js"></script>
    <script src="assets/js/Swiper-Slider-Card-For-Blog-Or-Product.js"></script>
</body>

</html>