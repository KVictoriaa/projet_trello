<?php
require_once "framework/Controller.php";
require_once "model/Card.php";
require_once "lib/base_64_encode_decode.php";
require_once "model/Participate.php";
require_once "model/Collaborate.php";
require_once "framework/utils.php";


class ControllerCard extends Controller {

    public function index(){
        $this->add();
    }
    
    public function add() { 
        $currentUser = $this->get_user_or_redirect();
        $errors = [];
        if (!isset($_POST["title"]) || !isset($_POST["IDColumn"]))
            $this->redirect("board");
        if (isset($_POST["title"]) && $_POST["title"] != "" && isset($_POST["IDColumn"]) && $_POST["IDColumn"] != "")
            $title= trim($_POST["title"]);
        $columnId = $_POST["IDColumn"];
        $column = Column::get(["id" => $columnId]);
        if(!$column)
                $this->redirect("board");

        $collaborate = Collaborate::get($column->board,$currentUser->id); 
        $lastposition = $column->getNumberCard();
        if(($currentUser->role != "admin") && $currentUser->id != $column->getBoard()->owner 
            && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
            
        else {
            
                $card = new Card(trim($_POST["title"]), "", $lastposition, new DateTime(),$currentUser->id, $columnId,null,null);
                
                $errors = Card::validateTitle($title);
                
                $errors = array_merge($errors, Card::validateUnicity($title,$column));
                if (empty ($errors)) {
                    $card->update();
                }
        }
        
        $this->redirect("board", "open", $column->getBoard()->id, base64url_encode(json_encode($errors)));
    }

    public function view() {
        $currentUser = $this->get_user_or_redirect();
        if (!isset($_GET["param1"]))
            $this->redirect();
        $cardId = $_GET["param1"];
        $card = Card::get(["id" => $cardId]);
        $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
        if (!$card)
            $this->redirect("board");
            if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
            && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
            
        else {
            (new View("cards/view"))->show(["card"=>$card]);
        }
    }

    public function confirm_delete() {
        $currentUser = $this->get_user_or_redirect();
        $id = $_GET["param1"] ?? null;
        if (!$id) $this->redirect("board");
        $card = Card::get(["id" => $id]);
        if (!$card)
            $this->redirect("board");

        if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
            && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
            
        else {
        (new View("cards/confirm_delete"))->show(array("card" => $card));
        }
    }

    public function delete() {
        $currentUser = $this->get_user_or_redirect();
        if (!isset($_POST["idCard"]) && $_POST["idCard"] !== "")
            $this->redirect();
        $cardId = $_POST["idCard"];
        $card = Card::get(["id" => $cardId]);
        $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
        if(!$card)
        $this->redirect("board");
        if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
        && $currentUser->id != $collaborate->Collaborator) {
        $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
        (new View("errors"))->show(array("errors" => $errors));
        }
        
        else {
            if ($card) {
                echo "enter";
                $board_id = $card->getColumn()->board;
                $card->delete();
            }

            if (!$board_id)
                $this->redirect();
        }
        $this->redirect("board", "open", $board_id);
    }

    public function edit(){
        $currentUser = $this->get_user_or_redirect(); 
        if(!isset($_GET["param1"]))
            $this->redirect();
        $cardId = $_GET["param1"];
        $card = Card::get(["id" => $cardId]);
        $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
       
        if (!$card)
            $this->redirect("board");
       
        if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
        && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
            
        else {
            $errors = [];
            if(isset($_POST["title"])&& isset($_POST["body"])){
                $oldTitle = $card->title;
                $title =$_POST["title"];
                $card->title=trim($_POST["title"]);
                $card->body=trim($_POST["body"]);
                if(isset($_POST["date"]))
                    $card->dueDate = $_POST["date"];
                
                //$errors = Card::validateTitle($title);
                if ($oldTitle != $card->title)
                    $errors = array_merge($errors, Card::validateTitle($title));

                if (empty($errors)) {
                    $card->update();
                    $this->redirect("board","open", $card->getColumn()->getBoard()->id);
                }
            }

            (new view("cards/edit"))->show(["card"=>$card, "errors" => $errors]);
        }
    }

    private function getCardByPost() {
        $currentUser = $this->get_user_or_redirect(); 

        $card = null;
        if (isset($_POST["id"]) && $_POST["id"] != null)
            $cardId = $_POST["id"];
            $card = Card::get(["id" => $cardId]);
    
        if (!$card)
            $this->redirect("board");

        if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner
        && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
            
        else {
            return $card;
        }
    }
    //******** Méthode pour Bouger les cartes ********/
    public function down()
    {
        $currentUser = $this->get_user_or_redirect();
        
        if (isset($_GET["param1"]) && $_GET["param1"] != "" ) {
            $cardId = $_GET["param1"];
            $card = Card::get(["id" => $cardId]);
            if(!$card)
                $this->redirect("board");
            $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
            
            if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
            && $currentUser->id != $collaborate->Collaborator) 
            {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            }  
            else {
                if ($card->position != ($card->getColumn()->getNumberCard() - 1)) {
                    $card->down();
                    $this->redirect("board", "open", $card->getColumn()->board);
                } 
            }
        }
    }

    public function up()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "" ) {
            $cardId = $_GET["param1"];
            $card = Card::get(["id" => $cardId]);
            if(!$card)
                $this->redirect("board");

            $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
        
            if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
                && $currentUser->id != $collaborate->Collaborator) {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            }  
            else {
                if ($card->position != 0) { 
                    $card->up();
                    $this->redirect("board", "open", $card->getColumn()->board);
                }
            }
        }
    }
    
    public function moveLeft()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $cardId = $_GET["param1"];
            $card = Card::get(["id" => $cardId]);
            if(!$card)
                $this->redirect("board");
            $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
        
            if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
                && $currentUser->id != $collaborate->Collaborator) {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            }  
            else {
                if ($card->getColumn()->position != 0) {
                    var_dump($card->getColumn()->board);
                    $card->moveCardLeft();
                    $this->redirect("board", "open", $card->getColumn()->board);
                } 
            }
        }
    }

    public function moveRight()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $cardId = $_GET["param1"];
            $card = Card::get(["id" => $cardId]);
            if(!$card)
                $this->redirect("board");
            $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
            if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner
            && $currentUser->id != $collaborate->Collaborator ) {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            } 
            else { 
                if ($card->getColumn()->position != ($card->getColumn()->getBoard()->getNumberColumn() - 1)) {
                    $card->moveCardRight();
                    
                    $this->redirect("board", "open", $card->getColumn()->board);
                } 
            }
        }
    }


    
    public function addParticipant(){
        $currentUser = $this->get_user_or_redirect();
        if(isset($_GET["param1"])) {
            $cardId = $_GET["param1"];
            $card = Card::get(["id"=>$cardId]);
            $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
            if(!$card)
                $this->redirect("board");

        }
        if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner
            && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }  
        else {
            if(isset($_POST["userId"])){
                $userId = $_POST["userId"];
                $participate = new Participate($userId, $cardId);
                $participate->add();
                $this->redirect("card", "addParticipant",$cardId);
            }
            (new View("cards/edit"))->show(array("errors"=>[],"card" => $card, "currentUser" => $currentUser));
        }
    }
    public function deleteParticipant(){
        $currentUser = $this->get_user_or_redirect();
        if(isset($_GET["param1"])){
            $cardId = $_GET["param1"];
            $card = Card::get(["id"=>$cardId]);
            $collaborate = Collaborate::get($card->getColumn()->board,$currentUser->id);  
            if(!$card){
                $this->redirect("board");
            }
        }
        if(($currentUser->role != "admin") && $currentUser->id != $card->getColumn()->getBoard()->owner 
        && $currentUser->id != $collaborate->Collaborator) 
        {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }  
        else {
            if(isset($_POST["userId"])){
                $userId = $_POST["userId"];
                $participate = Participate::get($userId, $cardId);
                $participate->delete();
                $this->redirect("card", "addParticipant",$cardId);
            }
            (new View("cards/edit"))->show(array("card" => $card , "currentUser" => $currentUser));
        }
    }
    
    public function calendar() {

        
        $currentUser = $this->get_user_or_redirect();
        $boarda = $currentUser->getBoardsAuthor();
        $collaborate = $currentUser->getBoardsCollaborate();
        $otherBoard = $currentUser->getOtherBoards();
        $boards = array_merge($boarda,$collaborate,$otherBoard);
        $colors = ['#000000','#FF0000','#00FF00','#0000FF','#FFFF00','#00FFFF','#FF00FF','#C0C0C0'];
        shuffle($colors);
        $cpt = 0;
        foreach ($boards as $board) {
            $board->color = $colors[$cpt];
            ++$cpt;
            
        }
        
        (new View("cards/calendar"))->show(array("boards"=>$boards, "currentUser" => $currentUser));
        
    }

   /********* Methode JS *********/

    public function duedate() {
        
        
        $currentUser = $this->get_user_or_redirect();
        $res = [];
        $columns = [];
        $boardsId = explode(',', $_POST["board"]);
        $colors =explode(',', $_POST["color"]);

        /*if(!$boardsId) {
            $this->redirect();
        }*/
        
        for ( $i = 0; $i < intval($_POST["size"]); ++$i) {
            $boards = $boardsId[$i];
              
            $board = Board::get(["id"=> $boards]);
            $board->color =$colors[$i];
            $columns = $board->getColumns();
            $cards = [];
            foreach ( $columns as $column) {
                
                $cards = array_merge($cards, $column->getCards());
                foreach ($cards as $card) {
                   //(if(intval(date("m", $card->dueDate))) ) 
                    //$card->title = $card->title;
                    $card->createdAt = $card->createdAt;
                    $card->start = $card->dueDate;
                    $card->color = $board->color;
                    //unset($card->author);
                    unset($card->column);
                    unset($card->modifiedAt);
                    unset($card->body);
                    //unset($card->id);
                    unset($card->position);
                    //unset($card->dueDate);
                    //unset($card->title);
                    //unset($card->createdAt);
                    
                }
                
            }
            $res = array_merge($res,$cards);
        }
        
        echo json_encode($res);
    }

    public function deleteJs() {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_POST["idCard"]) && $_POST["idCard"] !== "") {
            $cardId = $_POST["idCard"];
            $card = Card::get(["id" => $cardId]);
            $participants = $card->getParticipants();
            if ($card) {
                $board_id = $card->getColumn()->board;
                if (!empty($participants)) {
                    foreach ($participants as $currentUser) {
                        $participate = participate::get($currentUser->id, $cardId);
                        $participate->delete();
                    }
                }
                $card->delete();
            }
        }

        echo json_encode($card);
    }

    public function titleIsAavaible() {
        $res = "false";
        if((isset($_POST["title"])) && $_POST["title"] != "" && isset($_POST["IDColumn"])) {
        
            $title = $_POST["title"];
            $columnId = $_POST["IDColumn"];
            $column = Column::get(["id" => $columnId]);
            $card = Card::getCardByTitle($title,$column);
            if(!$card){
                $res = "true";
            }

        }
        echo $res;
    }
    public function moveCardDrag(){

        if(isset($_POST["startCard"])  && isset($_POST["startColumn"]) && isset($_POST["endColumn"])) {

            $startMoveCard = $_POST["startCard"];
            //$endMoveCard = $_POST["endCard"];
            $startMoveColumn = $_POST["startColumn"];
            $endMoveColumn = $_POST["endColumn"];
            Card::moveCardDragColumn($startMoveCard,$startMoveColumn, $endMoveColumn);
            echo "true";
        }
        echo "false";

    }
    public function moveCardDragUpDown() {
       
        if(isset($_POST["startCard"]) && isset($_POST["endCard"])) {

            $startMoveCard = $_POST["startCard"];
            $endMoveCard = $_POST["endCard"];
            //$startMoveColumn = $_POST["startColumn"];
            //$endMoveColumn = $_POST["endColumn"];
            Card::moveCardDragUpDown($startMoveCard, $endMoveCard);
            echo "true";
        }
        echo "false";

    }
    public function getCard() {
        if(isset($_GET["param1"])) {
            $cardId = $_GET["param1"];
            $card = Card::get(["id"=> $cardId]);
            echo json_encode($card);
        }
    }
}