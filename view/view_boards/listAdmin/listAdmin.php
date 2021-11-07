<?php require_once "view/template_top.php"; ?>

<script src="js/board.js" type="text/javascript"></script>

    <h2>Your boards</h2>
    <div class="d-flex flex-row flex-wrap">
        <?php if($currentUser->getBoardsAuthor()) { ?>
            <?php foreach ($currentUser->getBoardsAuthor() as $board) { ?>

                <a href="board/open/<?= $board->id ?>" class="no-decoration">
                    <div class="boards py-1 mx-2 mb-4">
                        <h5><?= $board->title ?>&nbsp(<?= $board->getNumberColumn(). " columns"?>)</h5>
                    </div>
                </a>

            <?php } ?>
        <?php } ?>
        <form action="board/add" method="post" id="formB">
            <div class="input-group mb-2">
                <label for="title"></label>
                <input type="text" class="form-control" name="title" id="title"  placeholder="Add board">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class='fas fa-plus'></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <?php if ($currentUser->getBoardsCollaborate()) { ?>
        <h2>Boards shared with you</h2>
        <div class="d-flex flex-row flex-wrap">
            <?php foreach ($currentUser->getBoardsCollaborate() as $board) { ?>
                <a href="board/open/<?= $board->id?>" class="no-decoration">
                    <div class="boards py-1 mx-2 mb-4">
                        <h5><?= $board->title ?>&nbsp(<?= $board->getNumberColumn(). " columns"?>) </h5>
                        <div>Owned by <?= $board->getOwner()->fullname ?></div>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if ($currentUser->role == 'admin') {?>
        <h2>Other's Boards</h2>

        <div class="d-flex flex-row flex-wrap">
                    <?php foreach ($currentUser->getOtherBoards() as $board): ?>
                        <?php if ($currentUser->role == 'admin') { ?>
                            <a href="board/open/<?= $board->id ?>" class="no-decoration">
                                <div class="boards other-boards py-1 mx-2 mb-4">
                                    <h5><?= $board->title ?> &nbsp(<?= $board->getNumberColumn(). " columns"?>)</h5>
                                        <div>Owned by <?= $board->getOwner()->fullname ?></div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php endforeach; ?>
            
        </div>
    <?php } ?>
<div>
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
</div>

<?php require_once "view/template_bottom.php"; ?>