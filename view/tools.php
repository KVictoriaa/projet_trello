<?php
function is_logged_user()
{

    return isset($_SESSION["user"]);
}

function get_logged_user()
{

    return is_logged_user() ? $_SESSION["user"] : null;
}

function get_full_name_or_guest()
{

    $logged_user = get_logged_user();
    return $logged_user ? $logged_user->fullname : "Guest";
}

function since($date)
{
    if (!$date)
        echo "never";

    else {
        $date = DateTime::createFromFormat("Y-m-d H:i:s", $date);
        $diff = $date->diff(new DateTime("now"));

        if ($diff->y > 0) {
            echo($diff->y . " year ago");
        } else if ($diff->m > 0) {
            echo($diff->m . " month ago");
        } else if ($diff->d > 0) {
            echo($diff->d . " day ago");
        } else if ($diff->h > 0) {
            echo($diff->i . " minute ago");
        } else if ($diff->s > 0)
            echo($diff->s . " second ago");
        else {
            echo " now";
        }
    }
}

?>