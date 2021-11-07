<?php
    require_once "tools.php";
?>
<?php require_once "template_top.php"; ?>
    <div>
        <div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4 class="alert-heading">Warning!</h4>
            <p class="mb-0">
                <?= $errors ?>
            </p>
        </div>
    </div>
<?php require_once "template_bottom.php"; ?>