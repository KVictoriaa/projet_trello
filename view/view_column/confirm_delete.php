<?php require_once "view/template_top.php"; ?>

    <div class="d-flex flex-column" style="align-items: center">
        <h1 style="text-align: center;"><?= $column->title ?></h1>

        <div style="text-align: center; border: solid 1px #ddd; border-radius: 5px;width: 600px;padding: 40px 0">
            <div>
                <i style="font-size: 10em; color: #E63749;" class="far fa-trash-alt"></i>
            </div>
            <div style="font-weight: bold; font-size: 2.5em; color: #E63749;">
                Are you sure ?
            </div>
            <hr style="margin: 20px 40px;">

            <div style="color: #E63749; font-size: 1.4em;">
                <p>Do you really want to delete this column ?</p>
                <p>This process canno't be undone.</p>
            </div>
            <div class="d-flex flex-row" style="justify-content: center">
                <div class="mx-1">
                    <a href="board/open/<?= $column->board ?>" class="btn btn-secondary">Cancel</a>
                </div>
                <form style="width: auto" class="mx-1" action="column/delete" method="post">
                    <input type="text" name="idColumn" value="<?= $column->id?>" hidden>
                    <input class="btn btn-danger" type="submit" value="Delete">
                </form>
            </div>
        </div>
    </div>

<?php require_once "view/template_bottom.php"; ?>