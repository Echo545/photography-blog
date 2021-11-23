<?php
include_once("../../rest.php");
include_once("../../lib.php");
include_once("../../api_lib.php");
header("HTTP/1.1 200");

$request = new RestRequest();
$db = connect_to_db();
$requestVars = $request->getRequestVariables();
$errors = [];
date_default_timezone_set('America/Los_Angeles');

// Get comments for post
if ($request->isGet())
{
    echo get_comments($db, $requestVars);
}

// Create new comment
elseif($request->isPost())
{
    new_comment($db, $requestVars);
}

// Update comment
elseif($request->isPut())
{
    update_comment($db, $requestVars);
}

// Delete comment
elseif($request->isDelete())
{
    delete_comment($db, $requestVars);
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