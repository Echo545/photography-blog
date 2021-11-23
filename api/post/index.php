<?php
include_once("../../rest.php");
include_once("../../lib.php");
include_once("../../api_lib.php");
header("HTTP/1.1 200");

$POST_PARAMS = ["post_text", "extra"];
$EXTRA_PARAMS = ["title", "image", "image_type"];
$DELETE_PARAMS = ["id"];

$request = new RestRequest();
$db = connect_to_db();
$requestVars = $request->getRequestVariables();
$errors = [];
date_default_timezone_set('America/Los_Angeles');

// Get all posts
if ($request->isGet())
{
    echo get_posts($db, $requestVars);
}

// Create new post
elseif($request->isPost())
{
    new_post($db, $requestVars);
}

// Update post
elseif($request->isPut())
{
    update_post($db, $requestVars);
}

// Delete Post
elseif($request->isDelete())
{
    delete_post($db, $requestVars);
}

// HANDLE INVALID REQUEST
else
{
    header("HTTP/1.1 400");
    array_push($errors, "Unsupported Request Type");
}

// Display errors
print_errors($errors);
?>