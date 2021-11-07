<?php require_once "view/template_top.php"; ?>
<?php require_once "view/tools.php"; ?>
<script src="js/boardCollaborate.js" type="text/javascript"></script>

<h2> Board "<?= $board->title ?> " : Collaborators </h2>


<h5> Current collaborator(s) : </h5>
<?php if ($board->getCollaborators()) {  ?>
    <?php foreach ($board->getCollaborators() as $collaborate) { ?>

        <div class="boards py-1 mx-2 mb-4" onclick="getCardCollaborate(<?= $collaborate->id ?>)">

            <h5><?= $collaborate->fullname ?> <?= $collaborate->mail ?></h5>
            <form style="width: auto" class="mx-1" method="post" action="board/confirm_deleteCollaborate/<?= $board->id ?>/<?= $collaborate->id ?>">
                    <input type="text" name="userId" value="<?= $collaborate->id ?>" hidden>
                    <button type="submit" class="fas fa-trash-alt"></button>
            </form>
        </div>
    <?php } ?>
<?php } else { ?>

<p>This board has no collaborator yet.</p>

<?php } ?>
<?php if($board->getNoCollaborator() != null) {?>
<form class="form-group" method="post" action="board/addCollaborator/<?= $board->id ?>">
    <label for="exampleSelect1">Add a new collaborator :</label>
    <select name="userId" class="form-control" id="exampleSelect1">
        <?php foreach ($board->getNoCollaborator() as $noCollaborate) { ?>
            <option name="user " value="<?= $noCollaborate->id ?>" ><?= $noCollaborate->fullname?> <?=$noCollaborate->mail ?></option>
        <?php } ?>
    </select>
    <button type="submit" class="btn btn-primary">
            <i class='fas fa-plus'></i>
    </button>
    <?php } ?>
</form>
<a class="btn btn-secondary" href="board/open/<?= $board->id?>">Back</a>

<div id="collaborate" title="Nombre de Participation">
    <p id= "nbParticipation"> </p>
    </div>
    
<?php require_once "view/template_bottom.php"; ?>