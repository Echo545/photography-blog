<?php

include_once("../../rest.php");
include_once("../../lib.php");
include_once("../../api_lib.php");

$request = new RestRequest();
$db = connect_to_db();
$requestVars = $request -> getRequestVariables();


// GET, show who is logged in
if($request->isGet())
{
    echo login_get();
}

// POST, login
elseif($request->isPost())
{
    login_user($db, $requestVars);
}

// PUT, make account
elseif($request->isPut())
{
    register_user($db, $requestVars);
}

// DELETE, logout
elseif($request->isDelete())
{
    user_logout();
}

?>