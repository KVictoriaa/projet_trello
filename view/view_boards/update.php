<?php require_once "view/template_top.php"; ?>

<?php foreach ($errors as $error) { ?>
    <?= $error ?>
<?php } ?>
<form method="post">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= $board->title ?>">
    <input class="btn btn-primary" type="submit" value="Edit Board">
    <a class="btn btn-secondary" href="board/open/<?= $board->id?>">Back</a>
</form>

<?php require_once "view/template_bottom.php"; ?>