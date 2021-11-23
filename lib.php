<?php
// Library of PHP functions & classes

/**
 * Simple sql request to specific DB.
 */
function sql_request($db, $sql, $sqlVar)
{
    // prepare the statement
    $statement = $db->prepare($sql);

    // Execute the query based in var type
    if (is_null($sqlVar))
    {
        $statement->execute();
    }
    elseif (is_array($sqlVar))
    {
        $statement->execute($sqlVar);
    }
    else
    {
        $statement->execute([$sqlVar]);
    }

    return $statement->fetch(PDO::FETCH_ASSOC);
}

/**
 * SQL Request using FetchAll.
 */
function sql_request_all($db, $sql, $sqlVar)
{
        $statement = $db->prepare($sql);

        if (is_null($sqlVar))
        {
            $statement->execute();
        }
        elseif (is_array($sqlVar))
        {
            $statement->execute($sqlVar);
        }
        else
        {
            $statement->execute([$sqlVar]);
        }

        return $statement->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Function to call an API.
 * The base of this function was taken from stackOverflow
 *
 * Data: array("param" => "value") ==> index.php?param=value
 */
function api_request($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
            {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);

            break;
        default:
            // Default is GET
            if ($data)
            {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}


/**
 * Makes sure that someone is logged in
 */
function verify_login()
{
    session_start();

    if (!array_key_exists("username", $_SESSION))
    {
        header("HTTP/1.1 400");
        $error = array("error_text" => "You must login to access this");
        echo json_encode($error);
        exit();
    }

    return true;
}

/**
 * Makes sure that the right user is logged in
 */
function user_logged_in($username)
{
    $logged_in = false;

    if (array_key_exists("username", $_SESSION))
    {
        $logged_in = strcmp($_SESSION["username"], $username) === 0;
    }

    return $logged_in;
}


/**
 * Checks if user exists in DB
 */
function user_exists($db, $username)
{
    $exists = false;

    $sql = "SELECT * FROM blog_user WHERE username = ?";
    $request = sql_request($db, $sql, $username);

    if (is_array($request) && !empty($request))
    {
        $exists = true;
    }

    return $exists;
}


function get_username($db, $id)
{
    $sql = "SELECT username FROM blog_user WHERE id = ?";
    $request = sql_request($db, $sql, $id);
    return $request["username"];
}


/**
 * Gets the user ID given a username
 */
function get_user_id($db, $username)
{
    $sql = "SELECT id FROM blog_user WHERE username = ?";
    $request = sql_request($db, $sql, $username);
    return $request["id"];
}


/**
 * Gets the ID of the user who made the given post
 */
function get_user_id_from_post($db, $post_id)
{
    $sql = "SELECT user_id FROM post WHERE id = ?";
    $request = sql_request($db, $sql, $post_id);
    return $request["user_id"];
}


function get_user_id_from_comment($db, $comment_id)
{
    $sql = "SELECT user_id FROM blog_comment WHERE id = ?";
    $request = sql_request($db, $sql, $comment_id);
    return $request["user_id"];
}


/**
 * Gets the image from a post ID
 */
function get_image_from_post($db, $post_id)
{
    $sql = "SELECT extra FROM post WHERE id = ?";
    $request = sql_request($db, $sql, $post_id);

    $extraDecoded = json_decode($request["extra"]);

    return ($extraDecoded -> image);
}


function get_post_from_id($db, $post_id)
{
    $sql = "SELECT * FROM post WHERE id = ?";
    $request = sql_request($db, $sql, $post_id);

    return $request;
}


/**
 * Verifies all keys are in array.
 */
function found_all_keys($inputs, $keys)
{
	$found_all = true;
	$msgs = array();

	foreach($keys as $key)
	{
		if(!array_key_exists($key, $inputs))
		{
			$found_all = false;

			array_push($msgs, "$key is missing");
		}
	}

	if(!$found_all)
	{
		$message = implode($msgs, ", ");

		$error = array("error_text" => $message);
		echo json_encode($error);
		exit();
	}
}

/**
 * Makes sure no extra keys are in array.
 */
function ensure_no_extra_keys($inputs, $keys)
{
	$msg = array();

	//check that there are no extra input parameters besides what is in keys
	foreach($inputs as $param => $value)
	{
		$param = trim($param);

		if(!in_array($param, $keys))
		{
			array_push($msg, "$param not a valid parameter");
		}
	}

	if(count($msg))
	{
		$message = implode($msg, ", ");
		$error = array("error_text" => $message);
		echo json_encode($error);
		exit();
	}
}



/**
 * Gets the largest post ID in the DB.
 */
function get_highest_post_id($db)
{
    $sql = "SELECT MAX(id) FROM post";
    $request = sql_request($db, $sql, NULL);

    return doubleval($request["max"]);
}

/**
 * Gets the largest comment ID in the DB.
 */
function get_highest_comment_id($db)
{
    $sql = "SELECT MAX(id) FROM blog_comment";
    $request = sql_request($db, $sql, NULL);

    return doubleval($request["max"]);
}


/**
 * Gets the largest user ID in the DB.
 */
function get_highest_user_id($db)
{
    $sql = "SELECT MAX(id) FROM blog_user";
    $request = sql_request($db, $sql, NULL);

    return doubleval($request["max"]);
}


/**
 * Ensures a given username is valid.
 */
function valid_username($name)
{
    $valid = false;

    if(strlen($name) > 1)
    {
        $valid = true;
    }

    return $valid;
}


function print_errors($errors)
{
    if (!empty($errors))
    {
        header("HTTP/1.1 400");
        $error = implode($errors, ", ");
        $message = array("error_text" => $error);
        echo json_encode($message);
    }
}

function get_post_id_from_comment_date($comment_date)
{
    // Start date for PHP date(0)
    $START_DATE = "1969-12-31";

    $date_old = new DateTime($START_DATE);
    $date_new = new DateTime($comment_date);

    $difference = $date_old -> diff($date_new);

    return $difference -> days;
}


function post_id_to_date($post_id)
{
    $DATE_MULTIPLIER = 86400;       // number of seconds in a day
    $date_id = date("Y-m-d", $DATE_MULTIPLIER * $post_id);

    return $date_id;
}


/**
 * Connect to pgsql db.
 */
function connect_to_db()
{
    // Example credentials for locally hosted pSQL DB
    $db = new PDO("pgsql:dbname=blog host=localhost user=dev password=dev");
    return $db;
}
?>