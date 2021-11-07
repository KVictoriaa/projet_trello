<?php require_once "view/template_top.php"; ?>
<?php require_once "view/tools.php"; ?>

    <h1>Card "<?= $card->title ?>" </h1>
 <div>Created By <?= $card->getAuthor()->fullname ?> <?php since($card->createdAt) ?>, modified <?php since($card->modifiedAt) ?></div>
    <div>This card is on the board "<?= $card->getColumn()->getBoard()->title ?>", column "<?= $card->getColumn()->title ?>" at position <?= $card->position + 1 ?></div>
<h2>Body</h2>
<div>
    <textarea style="width: 100%;" readonly><?= $card->body ?></textarea>

</div>
<h5>Due date</h5>
<?php  if( $card->dueDate != null) {?>
  <div>
    <input type="date" name="date" value="<?= $card->dueDate?>" placeholder="yyyy/mm/dd" />
</div>  
<?php } else {?>
<p>This card has no due date yet</p>
<?php } ?>
<?php if($card->getParticipants()) { ?>
<h3> Current participant(s) : </h3>
<?php foreach ($card->getParticipants() as $participate) { ?>
    <div class="boards py-1 mx-2 mb-4">
        <h5><?= $participate->fullname?> <?=$participate->mail ?></h5>
    </div>
<?php } ?>
<?php } else { ?>

<p>This card has no participant yet.</p>
<?php } ?>
<a class="btn btn-secondary" href="board/open/<?= $card->getColumn()->getBoard()->id ?>">Back</a>
<a href="card/confirm_delete/<?= $card->id ?>" class="btn btn-danger">Delete</a>
<a href="card/edit/<?= $card->id ?>" class="btn btn-primary">Edit</a>
<?php require_once "view/template_bottom.php"; ?>
