<?php
require_once "framework/Controller.php";
require_once "model/board.php";
require_once "model/Column.php";
require_once "model/Collaborate.php";
require_once "framework/utils.php";

class ControllerColumn extends Controller {


    public function index()
    {  
        $this->add();
     }
    
     public function add() {            
        
        $currentUser = $this->get_user_or_redirect();
        $title ="";
        if (!isset($_POST["IDBoard"]))
            $this->redirect(); 
        if(isset($_POST["title"]) && $_POST["title"] != "") {

            $boardId = $_POST["IDBoard"];
            $board = Board::get(["id" => $boardId]);
            $title = trim($_POST['title']);
            $lastposition = $board->getNumberColumn();
            $collaborate = Collaborate::get($boardId,$currentUser->id);
            $errors = Column::validateTitle($title);
            $errors = array_merge($errors, Column::validateUnicity($title,$boardId));
            if(($currentUser->role != "admin") && $currentUser->id != $board->owner 
                && $currentUser->id != $collaborate->Collaborator) {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            }
                
            else {
    
                if (empty($errors)) {
                        $column = new Column($title, $lastposition, new DateTime(), $boardId );
                        $column->update();
                    
                } 
                
            }
            $this->redirect("board", "open", $boardId , Utils::url_safe_encode($errors),Utils::url_safe_encode($title));
        } else {
            $this->redirect("board","open", $boardId);
        }
     }
    
    public function delete() {
        $currentUser = $this->get_user_or_redirect();
        $column = null;

        if (!isset($_POST["idColumn"]))
            $this->redirect();
        //$boardId = $_GET["param1"];
        //$board = Board::get(["id" => $boardId]);
        $columnId = $_POST["idColumn"];
        $column = Column::get(["id" => $columnId]);
        if(!$column)
            $this->redirect();
        $collaborate = Collaborate::get($column->board,$currentUser->id);
        $board_id = null;
        if (($currentUser->role != "admin") && $currentUser->id != $column->getBoard()->owner
            && $currentUser->id != $collaborate->Collaborator) {
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
        else {
            if ($column) {
                $board_id = $column->board;
                $cards = $column->getCards();
                if (!empty($cards)) {
                    foreach ($cards as $card) {
                        if (!empty($card->getParticipants())) {
                            foreach ($card->getParticipants() as $participant) {
                                $participate = Participate::get($participant->id, $card->id);
                                $participate->delete();
                            }
                        }
                        $card->delete();
                    }
                $column->delete();
                $this->redirect("board", "open", $column->board);
                }
               
            }
        }
        if (!$board_id)
            $this->redirect();
        $this->redirect("board", "open", $board_id);
    }

    public function confirm_delete() {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $columnId = $_GET["param1"];
        } 
        //$boardId = $_GET["param1"];
        //$board = Board::get(["id" => $boardId]);
        if (!$columnId) $this->redirect("board");
        $column = Column::get(["id" => $columnId]);
        $collaborate = Collaborate::get($column->board,$currentUser->id);
        if (!$column)
            $this->redirect("board");

        if (($currentUser->role != "admin") && $currentUser->id != $column->getBoard()->owner 
            && $currentUser->id != $collaborate->Collaborator){
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
        }
     
        else {        
            $cards = $column->getCards();
            if (empty($cards)) {
                /*foreach ($cards as $card) {
                    if (!empty($card->getParticipants())) {
                        foreach ($card->getParticipants() as $participant) {
                            $participate = Participate::get($participant->id, $card->id);
                            $participate->delete();
                        }
                    }
                    $card->delete();
                }*/
                $column->delete();
                $this->redirect("board", "open", $column->board);
            }

            (new View("column/confirm_delete"))->show(array("column" => $column,"currentUser"=>$currentUser));
        }
    }

    public function edit() {
        $currentUser = $this->get_user_or_redirect();
        if(!isset($_GET["param1"]))
            $this->redirect();
        $columnId = $_GET["param1"];
        //$boardId = $_GET["param2"];
        //$board = Board::get(["id" => $boardId]);
        $column = Column::get(["id" => $columnId]);
        $collaborate = Collaborate::get($column->board,$currentUser->id);
        if(!$column) 
            $this->redirect();
        if (($currentUser->role != "admin") && $currentUser->id != $column->getBoard()->owner 
        && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
            
        else {
            $errors = [];
            if(isset($_POST["title"])){
                $Title = trim($_POST["title"]);
                $errors = Column::validateTitle($Title);
                $errors = array_merge($errors, Column::validateUnicity($Title, $column->board));
                if (empty($errors)) {
                    $column->title=$_POST["title"];
                    $column->update();
                    $this->redirect("board","open", $column->board);
                }
            }

        (new view("column/edit"))->show(["column"=>$column, "errors" => $errors ?? [],"currentUser" => $currentUser]);
        }
        
    }

    private function getColumnByPost() {
        $column = null;
        if (isset($_POST["ID"]) && $_POST["ID"] != null)
            $columnId = $_POST["ID"];
        $column = Column::get(["id" => $columnId]);
        if (!$column)
            $this->redirect("board");
        return $column;
    }

    public function left()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "" ) {
            $columnId = $_GET["param1"];
            $column = Column::get(["id" => $columnId]);
            //$boardId = $_GET["param1"];
            //$board = Board::get(["id" => $boardId]);
            $collaborate = Collaborate::get($column->board,$currentUser->id);  
            if(($currentUser->role != "admin") && $currentUser->id != $column->getBoard()->owner
            && $currentUser->id != $collaborate->Collaborator ) {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            } 
            else { 
                if ($column->position != 0) {
                    $column->left();
                    $this->redirect("board", "open",$column->board );
                } 
            }
        }
    }

    public function right()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "" ) {
            $columnId = $_GET["param1"];
            $column = Column::get(["id" => $columnId]);
            //$boardId = $_GET["param1"];
            //$board = Board::get(["id" => $boardId]);
            $collaborate = Collaborate::get($column->board,$currentUser->id);  
            if(($currentUser->role != "admin") && $currentUser->id != $column->getBoard()->owner
            && $currentUser->id != $collaborate->Collaborator ) {
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
            } 
            else { 
                if ($column->position != ($column->getBoard()->getNumberColumn() - 1)) {
                    $column->right();
                    $this->redirect("board", "open", $column->board);
                } 
            }
        }
    }


    /********** Methode JS **********/
    
    public function titleIsAavaible() {
        $res = "false";
        if((isset($_POST["title"])) && $_POST["title"] != "" && isset($_POST["IDBoard"])) {
            $Title = $_POST["title"];
            $board = $_POST["IDBoard"];
            $column = Column::validateUnicity($Title,$board);
            if(!$column){
                $res = "true";
            }

        }
        echo $res;
    }
    public function deleteColumnJS() {
        $currentUser = $this->get_user_or_redirect();
        $columnId = $_POST["idColumn"];
        $column = Column::get(["id" => $columnId]);
       
        $board_id = null;
        
            if ($column) {
        
                $cards = $column->getCards();
                if (!empty($cards)) {
                    foreach ($cards as $card) {
                        if (!empty($card->getParticipants())) {
                            foreach ($card->getParticipants() as $participant) {
                                $participate = Participate::get($participant->id, $card->id);
                                $participate->delete();
                            }
                        }
                        $card->delete();
                    }
                }
                $column->delete();
                $this->redirect("board", "open", $column->board);
                
               
            }   
            echo json_encode($column);
           
        }
        
    public function moveDrag() {
        if(isset($_POST["startColumn"]) && isset($_POST["endColumn"])) {

            $startMoveColumn = $_POST["startColumn"];
            $endMoveColumn = $_POST["endColumn"];
            Column::moveColumnJs($startMoveColumn, $endMoveColumn);
            echo "true";
        }
        echo "false";

    }
}