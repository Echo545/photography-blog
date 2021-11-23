<?php
include_once("rest.php");
include_once("lib.php");
include_once("api_lib.php");

user_logout();

?>

<html>
    <body>
        <script>
            window.location.replace("/blog/");
        </script>
    </body>
</html>