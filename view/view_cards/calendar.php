<?php require_once "view/template_top.php"; ?>

<script src="js/calendar.js" type="text/javascript"></script>


    <form style="width: 20%; display: inline-flex;" class="mx-1" method="post"  id="onclick" >
        <?php  foreach($boards as $board) { ?>
            <div id="val"  >
                <input class="check" type="checkbox" name="board" id="board" value="<?= $board->id ?>" color="<?= $board->color ?>" checked/> 
            </div>
            <div class="input-group-append" id="color">
                <label for="<?= $board->id ?>" style="color:<?= $board->color ?>"><?= $board->title ?> </label>
            </div>
        <?php } ?>       
    </form>
    <div id="calendar"   >
    </div>

    <div id="cardPopUp" title="title">
    <p id= "cardTitle"> </p>
    <p id= "cardCreatedAt"> </p>
    <p id= "cardDuedate"> </p>
    </div>
    
    
<?php require_once "view/template_bottom.php"; ?>