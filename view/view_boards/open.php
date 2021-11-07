<?php require_once "view/template_top.php"; ?>
<?php require_once "view/tools.php"; ?>

<script src="js/column.js" type="text/javascript"></script>
<script src="js/columnMove.js" type="text/javascript"></script>
<script src="js/boardDelete.js" type="text/javascript"></script>
<script src="js/columnDelete.js" type="text/javascript"></script>
<script src="js/cardDelete.js" type="text/javascript"></script>
<script src="js/card.js" type="text/javascript"></script>
<script src="js/cardMove.js" type="text/javascript"></script>



<h1><?= $board->title ?></h1>
<h2>owned by <?= $board->getOwner()->fullname ?></h2>
<div> created <?php since($board->created_at) ?> by <?= $board->getOwner()->fullname ?>. modified <?php since($board->modified_at) ?></div>
<?php if ($currentUser &&  $currentUser->id == $board->owner  || $currentUser->role == "admin") { ?>
    <a href="board/update/<?= $board->id ?>"><i style="color: black" class="fas fa-edit"></i></a>
    <a href ="board/addCollaborator/<?= $board->id?>"><i style="color: black" class="fas fa-users"></i></a>
    <button onclick= "deleteBoard(<?= $board->id?>)" class="boardB"><i  style="color: red" class="fas fa-trash-alt"></i></button>
    <a id="boardD" href="board/confirm_delete/<?= $board->id?>"><i style="color: black" class="fas fa-trash-alt"></i></a>
    
<?php } ?>

<?php if (count($errors) != 0): ?>
    <div class='errors'>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="d-flex flex-column lists" style="justify-content: space-around;" >
    <section class="lists-container columnMoveBoard " >
        <?php foreach ($board->getColumns() as $column) { ?>
          <div class="list " id="<?= $column->id?>" >
            <div class="listColumn" idColumn = "<?= $column->id ?>">
                <h3 style="font-size: 20px!important;padding-left: 0.6rem"><?= $column->title ?></h3>
                <div style="padding-left: 0.6rem;" id="columnDelete" >
                    <button onclick= "deleteColumn(<?= $column->id?>)" class="columnB" ><i  style="color: red" class="fas fa-trash-alt" ></i></button>
                    <a id="columnD" class="column" href="column/confirm_delete/<?= $column->id ?>"><i style="color: black" class="fas fa-trash-alt"></i></a>
                    <?php if($column->position != 0) {?>
                        <form style="width: 10px; display: inline" action="column/left/<?= $column->id ?>" method="post" >
                            <input  type="text" name="ID" value="<?= $column->id ?>" hidden>
                            <button class="button-no-decoration leftColumn"  ><i class="fas fa-arrow-circle-left" id="rightColumn"></i></button>
                        </form>
                    <?php } ?>
                <?php if($column->position != ($board->getNumberColumn() - 1)) {?>
                <form style="display: inline" action="column/right/<?= $column->id ?>" method="post" >
                    <input type="text" name="ID" value="<?= $column->id ?>" hidden >
                    <button class="button-no-decoration rightColumn" ><i class="fas fa-arrow-circle-right" id="leftColumn"></i></button>
                </form>
                <?php } ?>
                
                <a href="column/edit/<?= $column->id ?>"><i style="color: black" class="fas fa-edit"></i></a>
                </div>
                <div class="cardMoveColumn" >
                <?php $cards = $column->getCards();
                    foreach ($cards as $card) { ?>
                        <ul class="list-items listCard" id="<?=$card->id ?>" idColumn = "<?= $column->id ?>">
                            <li  <?php if($card->dueDate != null && $card->isDuedate()){ echo 'style="background-color:red"';} ?>>
                                <div><a href="card/view/<?= $card->id ?>"><?= $card->title ?></a></div>
                                <div id="<?= $card->id ?>">
                                    <?php if ($card->position != ($column->getNumberCard() - 1)) { ?>
                                    <form style="display: inline" action="card/down/<?= $card->id ?>" method="post">
                                        <input type="text" name="id" value="<?= $card->id ?>" hidden>
                                        <button class="button-no-decoration downCard"><i class="fas fa-arrow-circle-down"></i></button>
                                    </form>
                                    <?php } ?>
                                    <?php if ($card->position != 0) { ?>
                                    <form style="display: inline" action="card/up/<?= $card->id ?>" method="post">
                                        <input type="text" name="id" value="<?= $card->id ?>" hidden>
                                        <button class="button-no-decoration upCard"><i class="fas fa-arrow-circle-up"></i></button>
                                    </form>
                                    <?php } ?>
                                    <?php if ($card->getColumn()->position != 0) { ?>
                                        <form style="display: inline" action="card/moveLeft/<?= $card->id ?>" method="post">
                                            <input type="text" name="id" value="<?= $card->id ?>" hidden>
                                            <button class="button-no-decoration leftCard"><i class="fas fa-arrow-circle-left"></i></button>
                                        </form>
                                    <?php } ?>

                                    <?php if ($card->getColumn()->position != ($board->getNumberColumn() -1)) { ?>
                                    <form style="display: inline" action="card/moveRight/<?= $card->id ?>" method="post">
                                        <input type="text" name="id" value="<?= $card->id ?>" hidden>
                                        <button class="button-no-decoration rightCard"><i class="fas fa-arrow-circle-right"></i></button>
                                    </form>
                                    <?php } ?>
                                    <a href="card/view/<?= $card->id ?>"><i style="color: black" class="fas fa-eye"></i></a>
                                    <a href="card/edit/<?= $card->id ?>"><i style="color: black" class="fas fa-edit"></i></a>
                                    <button onclick= "deleteCard(<?= $card->id?>)" class="cardB"><i  style="color: red" class="fas fa-trash-alt" ></i></button>

                                    <a id="cardD" class="card" href="card/confirm_delete/<?= $card->id?>"><i style="color: black" class="fas fa-trash-alt"></i></a>
                                </div>

                            </li>
                        </ul>
                <?php } ?>
                </div>
                <form style="padding-left: 0.6rem;" action="card/add" method="post" id=<?= "formCard".$column->id ?>>
                    <div class="input-group mb-2">
                        <label for="<?= $column->id ?>IDColumn"></label>
                        <input type="text" name="IDColumn" id="<?= $column->id ?>" value="<?= $column->id ?>" hidden>
                        <label for="<?= $column->id ?>title"></label>
                        <input type="text" class="form-control" name="title" id="<?= "title".$column->id ?>" oninput="doValidate(<?= $column->id ?>)"  placeholder="Add card">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class='fas fa-plus'></i>
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        <?php } ?>
        <form action="column/add" method="post" id="formC">
            <div class="input-group mb-2">

                <input type="text" name="IDBoard" id="IDBoard" value="<?= $board->id ?>" hidden>
                <input type="text" class="form-control" name="title" id="title" value="<?= $title ?>" placeholder="Add column">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class='fas fa-plus'></i>
                    </button>
                </div>
            </div>
        </form>
        
    </section>
<div class="suppressionBoard" title="suppression de board" id="confirmDeleteBoard" >
    <span id ="BoardSuppression"></span>
</div>
<div class="suppressionColumn" title="suppression de column" id="confirmDeleteColumn" >
    <span id ="columnSuppression"></span>
</div>
<div class="" title="suppression de card" id="confirmDeleteCard" >
    <span id ="cardSuppression"></span>
</div>
</div>

<?php require_once "view/template_bottom.php"; ?>
