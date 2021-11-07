<?php require_once "view/template_top.php"; ?>

    <div>
        <h1>Edit a card</h1>
        <div>Created By <?= $card->getAuthor()->fullname ?> <?php since($card->createdAt) ?>.</div>
    </div>
    <form method="post" action="card/edit/<?= $card->id ?>">
        <label style="width: 100%;" for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= $card->title ?>">
        <label style="width: 100%;">Body</label>
        <textarea id="body" name="body"><?= $card->body ?></textarea>
        <h5>Due date</h5>
        <input type="date" name="date" value="<?= $card->dueDate?>" placeholder="yyyy/mm/dd" />
        <h2>Board</h2>
        <div style="background: #ddd; border-radius: 5px;padding: 5px;"><?= $card->getColumn()->getBoard()->title ?></div>
        <h2>Column</h2>
        <div style="background: #ddd; border-radius: 5px;padding: 5px;"><?= $card->getColumn()->title ?></div>
        <div style="margin-top: 10px;">
            <input class="btn btn-primary" type="submit" value="edit card">
            <a class="btn btn-secondary" href="board/open/<?= $card->getColumn()->getBoard()->id ?>">Cancel</a>
        </div>

    </form>
<?php if($card->getParticipants() ) { ?>
    <h3> Current participant(s) : </h3>
<?php foreach ($card->getParticipants() as $participate) { ?>

    <div class="boards py-1 mx-2 mb-4">
        <h5><?= $participate->fullname?> <?= $participate->mail ?></h5>

        <form style="width: auto" class="mx-1" method="post" action="card/deleteParticipant/<?= $card->id ?>">
            <input type="text" name="userId" value="<?= $participate->id ?>" hidden>
            <button type="submit" class="fas fa-trash-alt">
        </form>
    </div>
<?php } ?>
<?php } else { ?>

    <p>This card has no participant yet.</p>
<?php } ?>

<?php if($card->noParticipant() != null) { ?>
    <form class="form-group" method="post" action="card/addParticipant/<?= $card->id ?>">
        <label for="exampleSelect1">Add a new participant :</label>
        <select name="userId" class="form-control" id="exampleSelect1">

            <?php foreach ($card->noParticipant() as $noParticipant) { ?>
                <option name="user" id="userId" value="<?= $noParticipant->id ?>" ><?= $noParticipant->fullname ?> <?=$noParticipant->mail ?></option>
            <?php } ?>
        </select>
        <button type="submit" class="btn btn-primary">
            <i class='fas fa-plus'></i>
        </button>
    </form
<?php } ?>
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