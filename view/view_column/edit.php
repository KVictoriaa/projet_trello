<?php require_once "view/template_top.php"; ?>

<div>
    <h1>Edit a column</h1>
</div>
<form method="post" id="edit" action="column/edit/<?= $column->id?>/<?= $column->getBoard()->id ?>">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= $column->title?>" >

    <h2>Board</h2>
    <div style="background: #ddd; border-radius: 5px;padding: 5px;"><?= $column->getBoard()->title ?></div>
    <div style="margin-top: 10px;">
        <input class="btn btn-primary" type="submit" value="edit column">
        <a class="btn btn-secondary" href="board/open/<?= $column->getBoard()->id ?>">Cancel</a>
    </div>

</form>

<?php if (count($errors) != 0): ?>
<div class='errors'>
    <p>Please correct the following error(s) :</p>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php require_once "view/template_bottom.php"; ?>