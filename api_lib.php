<?php
include_once("rest.php");
include_once("lib.php");
header("HTTP/1.1 200");

date_default_timezone_set('America/Los_Angeles');

/**
 * Gets all posts in the blog
 */
function get_posts($db, $requestVars)
{
    $errors = [];

    if (array_key_exists("username", $requestVars))
    {
        $username = $requestVars["username"];

        if (user_exists($db, $username))
        {
            $sql = "SELECT * FROM post WHERE user_id = ? ORDER BY id DESC";
            $data = get_user_id($db, $username);
            $results = sql_request_all($db, $sql, $data);
        }
        else
        {
            // Invalid username
            array_push($errors, "Invalid username");
        }
    }
    else
    {
        $sql = "SELECT * FROM post ORDER BY id DESC";
        $results = sql_request_all($db, $sql, NULL);
    }

    // display success if no errors
    if(empty($errors))
    {
        // header("HTTP/1.1 200");
        // echo json_encode($results);
    }
    else
    {
        print_errors($errors);
    }

    return json_encode($results);
}

function new_post($db, $requestVars)
{
    $POST_PARAMS = ["post_text", "extra"];
    $EXTRA_PARAMS = ["title", "image", "image_type"];
    $errors = [];

    // Verify login
    verify_login();

    // verify array_key_exists in request
    found_all_keys($requestVars, $POST_PARAMS);
    found_all_keys($requestVars["extra"], $EXTRA_PARAMS);

    // Process heading & image from extra
    $post_text = $requestVars["post_text"];
    $post_extra = $requestVars["extra"];
    $post_title = $post_extra["title"];
    $post_image = $post_extra["image"];
    $post_image_type = $post_extra["image_type"];

    // Get right ID's and date for the sql request
    $user_id = get_user_id($db, $_SESSION["username"]);
    $post_id = get_highest_post_id($db) + 1;

    // Write the image to the images directory
    $filename = "../../images/" . $post_id . $post_image_type;
    file_put_contents($filename, base64_decode($post_image));

    $date = date('Y-m-d');

    $extra = array();
    $extra["post_title"] = $post_title;
    $extra["image"] = $filename;
    $extra = strval(json_encode($extra));

    $sql = "INSERT INTO post (id, user_id, post_date, post_text, extra) VALUES (?, ?, ?, ?, ?)";
    $data = [$post_id, $user_id, $date, $post_text, $extra];

    $request = sql_request($db, $sql, $data);

    // display success if no errors
    if(empty($errors))
    {
        header("HTTP/1.1 200");
        echo "Successfully created post";
    }
    else
    {
        print_errors($errors);
    }

    return json_encode($request);
}


function update_post($db, $requestVars)
{
    $UPDATE_PARAMS = ["post_title", "post_text", "post_id"];
    verify_login();
    found_all_keys($requestVars, $UPDATE_PARAMS);

    // New data
    $post_id = $requestVars["post_id"];
    $post_title= $requestVars["post_title"];
    $post_text = $requestVars["post_text"];

    // Validate
    $username = $_SESSION["username"];
    $user_id = get_user_id($db, $username);
    $post_owner_id = get_user_id_from_post($db, $post_id);

    if ($post_owner_id == $user_id)
    {
        // Get old data
        $image = get_image_from_post($db, $post_id);

        // Craft new extra field
        $extra = array();
        $extra["post_title"] = $post_title;
        $extra["image"] = $image;
        $extra = strval(json_encode($extra));

        $data = [$post_text, $extra, $post_id];

        // Make request
        $sql = "UPDATE post SET post_text = ?, extra = ? WHERE id = ?";
        $request = sql_request($db, $sql, $data);

        echo "Updated Post!";
    }
    else
    {
        // Return nothing
    }
}


function delete_post($db, $requestVars)
{
    $DELETE_PARAMS = ["id"];
    $errors = [];

    // Initial verification
    verify_login();
    found_all_keys($requestVars, $DELETE_PARAMS);

    $post_id = $requestVars["id"];
    $username = $_SESSION["username"];

    // Make sure post ID is a valid int
    if (is_int( (int) $post_id))
    {
        $user_id = get_user_id($db, $username);
        $post_owner_id = get_user_id_from_post($db, $post_id);

        // Make sure the post is made by the current user
        if ($post_owner_id == $user_id)
        {
            // Delete associated image
            $filename = get_image_from_post($db, $post_id);
            if (! unlink($filename))
            {
                array_push($errors, "Failed to delete image");
            }

            // Delete from DB
            $sql = "DELETE FROM post WHERE id = ?";
            $request = sql_request($db, $sql, $post_id);
        }
        else
        {
            array_push($errors, "You cannot delete a post you did not make");
        }
    }
    else
    {
        array_push($errors, "Invalid post ID");
    }


    // display success if no errors
    if(empty($errors))
    {
        header("HTTP/1.1 200");
        echo "Successfully deleted post";
    }
    else
    {
        print_errors($errors);
    }
}


/**
 * Gets the user logged in
 */
function login_get()
{
    // Join the session if it exists
    session_start();

    // Check if anyone is logged in
    if (array_key_exists("username", $_SESSION))
    {
        $user = $_SESSION["username"];
    }
    else
    {
        $user = NULL;
    }

    return $user;
}


function login_user($db, $requestVars)
{
    $success = false;

    if (array_key_exists("username", $requestVars) && array_key_exists("password", $requestVars))
    {
        $username = $requestVars["username"];
        $password = $requestVars["password"];

        // execute the query
        $sql = "SELECT password FROM blog_user WHERE username = ?";
        $result = sql_request($db, $sql, $username);

        // process results
        if (is_array($result))
        {
            $pw = $result["password"];
            $crpyt = password_hash($password, PASSWORD_DEFAULT);

            // check if password matches the saved encrpyred password
            if (password_verify($password, $crpyt))
            {
                // create a new session
                session_start();
                $_SESSION["username"] = $username;
                $success = true;

                echo $success;

            }
            else
            {
                // unauthorized
                http_response_code(403);

                // TODO change this
                // echo "BAD PASSWORD";
            }
        }
        else
        {
            // TODO change this
            // echo "BAD USERNAME";
        }
    }
    else
    {
        http_response_code(403);
    }

    return $success;
}


function register_user($db, $requestVars)
{
    $success = false;

    $USER_PARAMS = ["username", "password"];
    found_all_keys($requestVars, $USER_PARAMS);

    $username = $requestVars["username"];
    $password = $requestVars["password"];

    if(!user_exists($db, $username))
    {
        $id = get_highest_user_id($db) + 1;

        // Make user
        $encryptedPW = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO blog_user (id, username, password) VALUES (?, ?, ?)";
        $data = array($id, $username, $encryptedPW);
        $request = sql_request($db, $sql, $data);

        // Log them in
        session_start();
        $_SESSION["username"] = $username;
        $success = true;
    }
    else
    {
        // echo "USER ALREADY EXISTS";
    }

    echo $success;
    return $success;
}


function user_logout()
{
    session_start();
    session_destroy();
}


/**
 * Gets all comments for a post
 */
function get_comments($db, $requestVars)
{
    if (array_key_exists("comment_id", $requestVars))
    {
        $comment_id = $requestVars["comment_id"];
        $sql = "SELECT comment_text FROM blog_comment WHERE id = ?";
        $request = sql_request($db, $sql, $comment_id);

        if (array_key_exists("comment_text", $request))
        {
            $results = $request["comment_text"];
        }
        else
        {
            $results = "";
        }
    }
    else
    {
        $COMMENT_PARAMS = ["post_id"];

        found_all_keys($requestVars, $COMMENT_PARAMS);

        $post_id = $requestVars["post_id"];
        // Because of the odd structure of the comment table (ie not having an associated post ID)
        // We're being tricky and using the date to refrence the post ID ;)

        $post_id_date = post_id_to_date($post_id);
        $sql = "SELECT * FROM blog_comment WHERE comment_date = ?";
        $results = json_encode(sql_request_all($db, $sql, $post_id_date));
    }

    return $results;
}


function new_comment($db, $requestVars)
{
    $COMMENT_PARAMS = ["post_id", "comment_text"];

    found_all_keys($requestVars, $COMMENT_PARAMS);
    verify_login();

    $id = get_highest_comment_id($db) + 1;
    $user_id = get_user_id($db, $_SESSION["username"]);
    $comment_text = $requestVars["comment_text"];
    $post_id = $requestVars["post_id"];

    $date = post_id_to_date($post_id);

    $sql = "INSERT INTO blog_comment (id, user_id, comment_text, comment_date) VALUES (?, ?, ?, ?)";
    $data = [$id, $user_id, $comment_text, $date];
    $results = sql_request($db, $sql, $data);

    if (is_array($results))
    {
        echo "Successly made comment";
    }
}


function delete_comment($db, $requestVars)
{
    $COMMENT_PARAMS = ["id"];
    found_all_keys($requestVars, $COMMENT_PARAMS);
    verify_login();

    $username = $_SESSION["username"];
    $comment_id = $requestVars["id"];

    $user_id = get_user_id($db, $username);
    $comment_owner_id = get_user_id_from_comment($db, $comment_id);

    // Make sure you can only delete comments you made
    if ($comment_owner_id == $user_id)
    {
        $sql = "DELETE FROM blog_comment WHERE id = ?";
        $results = sql_request($db, $sql, $comment_id);
    }

    if (is_array($results))
    {
        echo "Successfully deleted comment";
    }
}


function update_comment($db, $requestVars)
{
    $COMMENT_PARAMS = ["id", "comment_text"];
    found_all_keys($requestVars, $COMMENT_PARAMS);
    verify_login();

    $username = $_SESSION["username"];
    $comment_id = $requestVars["id"];
    $comment_text = $requestVars["comment_text"];

    $user_id = get_user_id($db, $username);
    $comment_owner_id = get_user_id_from_comment($db, $comment_id);

    // Make sure you can only edit comments you made
    if ($comment_owner_id == $user_id)
    {
        $sql = "UPDATE blog_comment SET comment_text = ? WHERE id = ?";
        $data = [$comment_text, $comment_id];
        $results = sql_request($db, $sql, $data);
    }

    if (is_array($results))
    {
        echo "Successfully updated comment";
    }
}

?>