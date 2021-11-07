<?php require_once "view/template_top.php"; ?>

    <div class="d-flex flex-column" style="align-items: center">
        <h1 style="text-align: center;"><?= $board->title ?></h1>

        <div style="text-align: center; border: solid 1px #ddd; border-radius: 5px;width: 600px;padding: 40px 0">
            <div>
                <i style="font-size: 10em; color: #E63749;" class="far fa-trash-alt"></i>
            </div>
            <div style="font-weight: bold; font-size: 2.5em; color: #E63749;">
                Are you sure ?
            </div>
            <hr style="margin: 20px 40px;">

            <div style="color: #E63749; font-size: 1.4em;">
                <p>Do you really want to delete this collaborator ?</p>
                <p>This process canno't be undone.</p>
            </div>
            <div class="d-flex flex-row" style="justify-content: center">
                <div class="mx-1">
                    <a href="board/addCollaborator/<?= $collaborate->board->id ?>" class="btn btn-secondary">Cancel</a>
                </div>
                <!-- <form style="width: auto" class="mx-1" action="board/deleteCollaborator/<?= $board->id ?>/<?= $collaborate->id ?>/<?= $collaborate->id ?>" method="post">
                    <input type="text" name="id" value="<?= $collaborate->id ?>" hidden>
                    <input class="btn btn-danger" type="submit" value="Delete">
                </form>-->
                <div class="mx-1">
                    <a href="board/confirm_deleteCollaborate/<?= $board->id ?>/<?= $collaborate ?>/<?= $collaborate ?>" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

<?php require_once "view/template_bottom.php"; ?>