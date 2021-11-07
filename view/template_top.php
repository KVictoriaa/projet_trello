<?php
require_once "tools.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <base href="<?= $web_root ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/style.css" />

    <link href="lib/fullcalendar-5.6-1.0/lib/main.css" rel="stylesheet"/>
    <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.css" rel="stylesheet"/>
    <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.structure.min.css" rel="stylesheet"/>

    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.js"></script> 
    <script src="lib/jquery-validation-1.19.0/jquery.validate.min.js" type="text/javascript"></script>
    <script src="lib/fullcalendar-5.6-1.0/lib/main.js" type="text/javascript"></script>
   



    <title>Trello</title>
</head>
<body>
<nav style="background:#007BFF" class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#"><?php echo get_full_name_or_guest() ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php if (is_logged_user()) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="board">Boards</a>
                </li> 
                <a class="nav-link" href="card/calendar">calendar</a>
            <?php } ?>
        </ul>
        <?php if (is_logged_user()) { ?>
            <a style="color:white;" href="user/logout"><i class="fas fa-sign-out-alt"></i></a>
        <?php } else { ?>

            <a style="color:white;" href="user/signup"><i style="color:white;" class="fas fa-user-plus"></i></a>
        <?php } ?>
    </div>
</nav>
<div id="page">
