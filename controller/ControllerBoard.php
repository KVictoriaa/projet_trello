<?php
require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/board.php";
require_once "model/Column.php";
require_once "lib/base_64_encode_decode.php";
require_once "model/Collaborate.php";
require_once "model/Card.php";
require_once "model/Participate.php";
require_once "framework/utils.php";

class ControllerBoard extends Controller
{

    public function index()
    {
        $currentUser = $this->get_user_or_redirect();
        
        $errors = [];
        if (isset($_GET["param1"]))

        
            $errors = json_decode(base64url_decode($_GET["param1"]));
        
                (new View("boards/listAdmin/listAdmin"))->show(array("errors" => $errors,'currentUser' => $currentUser));
        
    }

    public function add()
    {
        $currentUser = $this->get_user_or_redirect();
        if (!$currentUser)
            $this->redirect("user", "login");
        $errors = [];
        if (isset($_POST['title']) && $_POST['title'] != "") {
            $title = trim($_POST['title']);
            $errors = Board::validateTitle($title);
            $errors = array_merge($errors, Board::validateUnicity($title));
            if (empty($errors)) {
                $board = new Board( $title,$currentUser->id);
                $board->update();
            }
            if(isset($_GET["param2"])){
                $title = Utils::url_safe_decode($_GET["param2"]);  
            }
        }
        $this->redirect('Board', 'index', base64url_encode(json_encode($errors)));
    }

    public function open()
    {
        $title="";
        $errors = [];
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $boardId = $_GET["param1"];
            $board = Board::get(["id" => $boardId]);
        }
        if(isset($_GET["param2"])){

            if(is_array(Utils::url_safe_decode($_GET["param2"])) && count(Utils::url_safe_decode($_GET["param2"])) !== 0) {
                $errors[] = Utils::url_safe_decode($_GET["param2"])[0];
            }
        }

        if(isset($_GET["param3"])){
            $title = Utils::url_safe_decode($_GET["param3"]);  
        }
        if (!$boardId) 
        $this->redirect("board");
        $collaborate = Collaborate::get($boardId,$currentUser->id);  
        if (!$board)
            $this->redirect("board");
        if(($currentUser->role != "admin") && $currentUser->id != $board->owner 
            && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }     
        else {
            (new View("boards/open"))->show(array("board" => $board, 'currentUser' => $currentUser, "errors"=>$errors, "title" =>$title));
        }
    

    }

    public function confirm_delete()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"])) {
            $id = $_GET["param1"];
        } else {
            $id = null;
        }
        if (!$id) $this->redirect("board");
        $board = Board::get(["id" => $id]);
        $collaborate = Collaborate::get($id,$currentUser->id);  
        $columns = $board->getColumns();
        if(($currentUser->role != "admin") && $currentUser->id != $board->owner
            && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
        else{
            if (!$board)
            $this->redirect("board");

        if (empty($board->getColumns()) && empty($board->getCollaborators())) {
            $board->delete();
            $this->redirect("board");
            
        }
        (new View("boards/confirm_delete"))->show(array("board" => $board));
    }
}

    public function delete()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_POST["id"])) {
            $id = $_POST["id"];
            $board = Board::get(["id" => $id]);
            $collaborate = Collaborate::get($id,$currentUser->id);  
            $columns = $board->getColumns();
        } else {
            $id = null;
        }
        if (!$id)
            $this->redirect("board");

        if(($currentUser->role != "admin") && $currentUser->id != $board->owner
            && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
        else {
            if (!$board)
            $this->redirect("board");
            if($board){
                if (!empty($columns)) {
                    foreach ($columns as $column) {
                        if (!empty($column->getCards())) {
                            foreach ($column->getCards() as $card) {
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
                    }
                }
                if (!empty($board->getCollaborators())) {
                    foreach ($board->getCollaborators() as $collaborators) {
    
                        $collaborator = Collaborate::get($id,$collaborators->id);
                        $collaborator->delete();
                    }
                }

                
            }
            $board->delete();
            $this->redirect("board");
        }
        
    }

//*********Pour modifier le titre du Board */
    public function update()
    {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"])) {
            $id = $_GET["param1"];
            $board = Board::get(["id" => $id]);

        } else {
            $id = null;
        }
        if (!$id) $this->redirect("board");
        if(($currentUser->role != "admin") && $currentUser->id != $board->owner
            && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
        else {
            if (!$board)
            $this->redirect("board");
        $errors = [];
        if (count($_POST) > 0) {
            if (isset($_POST['title']) && $_POST['title'] != "") {
            $title = trim($_POST["title"]);
            $errors = Board::validateTitle($title);
            if ($title != $board->title)
                $errors = array_merge($errors, Board::validateUnicity($title));
            $board->title = $title;
            if (empty($errors)) {
                $board->update();
                $this->redirect("board", "open", $board->id);
            }
        }
        }

        (new View("boards/update"))->show(array("board" => $board, "errors" => $errors));
        }
        
    }

    public function addCollaborator(){
        $currentUser = $this->get_user_or_redirect();
        if(isset($_GET["param1"]) && $_GET["param1"] != ""){
            $boardId = $_GET["param1"]; 
            $board = Board::get(["id" => $boardId]);
        }
        /*if(!$boardId) {
            $this->redirect("board");
        }*/
        
        if(!$board){
            $this->redirect("board");
        }
        if(($currentUser->role != "admin") && $currentUser->id != $board->owner
            && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("errors"))->show(array("errors" => $errors));
        }
        
        else {
            if(isset($_POST["userId"])){
                $userId = $_POST["userId"];
                if(!$userId) {
                    $this->redirect("board");
                }
                $collaborate = new Collaborate( $boardId,$userId);
                if(!$collaborate){
                    $this->redirect("board");
                }
                else {
                    $collaborate->add();
                    $this->redirect("board", "addCollaborator",$boardId);
                }
            }
        (new View("collaborate/add"))->show(array("board" => $board, "currentUser" => $currentUser));
        }
    }

    public function deleteCollaborator(){
        $currentUser = $this->get_user_or_redirect();
        if(isset($_GET["param1"])){
            $boardId = $_GET["param1"];
            $board = Board::get(["id" => $boardId]);
        }
        if(!$board) {
            $this->redirect("board");
        }
        if(($currentUser->role != "admin") && $currentUser->id != $board->owner
            && $currentUser->id != $collaborate->Collaborator){
            $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
            (new View("error"))->show(array("errors" => $errors));
        }
        else {
            if(isset($_POST["userId"])){
                $userId = $_POST["userId"];
                $user= User::getUserById(["id" => $userId]);
                $collaborate = Collaborate::get( $boardId,$userId);
                $columns = $boardId->getColumns();
                foreach ($columns as $column){
                    $cards = $columns->getCards();
                    foreach ($cards as $card) {
                        $cols = Participate::get($userId,$card->ID);
                        $participates = $card->getParticipant();
                        if(in_array($user,$participates)){
                            $cols->delete();
                        }
                    }
                }
                $collaborate->delete();
                $this->redirect("board", "addCollaborator",$boardId);
            }
        
        (new View("collaborate/add"))->show(array("board" => $board, "currentUser" => $currentUser));
       } 
    }

    public function confirm_deleteCollaborate() {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $boardId = $_GET["param1"];
            $board = Board::get(["id" => $boardId]);
            
        }
        
    
        if (($currentUser->role != "admin") && $currentUser->id != $board->owner
            && $currentUser->id != $collaborate->Collaborator){
                $errors = "vous n'avez pas les accès neccessaire pour effectuer cette action!";
                (new View("errors"))->show(array("errors" => $errors));
        }
        else {
            if(isset($_GET["param3"])){
                $userId = $_GET["param3"];
                $user= User::getUserById($userId);

               $collaborate = Collaborate::get( $boardId,$userId);
                $columns = $board->getColumns();
                foreach ($columns as $column){
                    $cards = $column->getCards();
                    foreach($cards as $card) {
                        foreach ($card->getParticipants() as $participant) {
                        $cols = Participate::get($participant->id,$card->id);
                        $participates = $card->getParticipants();
                        if(in_array($user,$participates)){
                            $cols->delete();}
                        }
                    }
                }
                $collaborate->delete();
                $this->redirect("board", "addCollaborator",$boardId);
            }
            (new View("collaborate/confirm_delete"))->show(array("board" => $board, "currentUser" => $currentUser, "collaborate"=>$_GET["param2"]));
        }
    }

    /********** Methode Js ***********/

    public function titleIsAvailableB() {
        $res = "true";
        if((isset($_POST["title"])) && trim($_POST["title"]) != "") {
            $title = trim($_POST["title"]);
            $board = Board::get(["title" => $_POST["title"]]);
            if($board){
                $res="false";
            }
        }
        echo $res;
    }

    public function deleteJs() {
        $currentUser = $this->get_user_or_redirect();
        if (isset($_POST["id"]) && $_POST["id"] !== "") {
            $id = $_POST["id"];
            $board = Board::get(["id" => $id]);
            $columns = $board->getColumns();
            if ($board) {
                if (!empty($columns)) {
                    foreach ($columns as $column) {
                        if (!empty($column->getCards())) {
                            foreach ($column->getCards() as $card) {
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
                    }
                }
                if (!empty($board->getCollaborators())) {
                    foreach ($board->getCollaborators() as $collaborator) {
    
                        $collaborate = Collaborate::get($collaborator->id, $board->id,);
                        $collaborate->delete();
                    }
                }
                $board->delete();
            }
        }

        echo json_encode($board);
    }

    public function getParticipipation() {
        if(isset($_GET["param1"])) {
            $collaborateId = $_GET["param1"];
            $collaborate = Collaborate::getNbParticipation($collaborateId);
            
            echo json_encode($collaborate);

        }
    }


    /*public function delete_pop_up(){
        
        if( isset($_POST['boardId'])) {
            $boardId = $_POST['boardId'];
            $board = Board::get(["id" => $boardId]);
            $columns = $board->getColumns();
         if($board){
            if (!empty($columns)) {
                foreach ($columns as $column) {
                    if (!empty($column->getCards())) {
                        foreach ($column->getCards() as $card) {
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
                }
            }
            if (!empty($board->getCollaborators())) {
                foreach ($board->getCollaborators() as $collaborator) {

                    $collaborate = Collaborate::get($collaborator->id, $board->id,);
                    $collaborate->delete();
                }
            }

            $board->delete();
           
         }
         echo json_encode ($board);
            
        }
        
    }*/
    
}

