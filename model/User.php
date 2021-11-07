<?php

require_once "framework/Model.php";

class User extends Model{

    public $id;
    public $mail;
    public $fullname;
    public $hashed_password;
    public $registeredAt;
    public $role;

    public function __construct($mail,$fullname ,$hashed_password,$role,$registeredAt=null, $id = -1){
        $this->mail = $mail;
        $this->fullname = $fullname;
        $this->hashed_password= $hashed_password;
        $this->registeredAt = $registeredAt; 
        $this->id = $id;
        $this->role = $role;
    }

    //******* Methode Static ******//

    public static function getUserByMail($mail) {
        $query = self::execute("Select * from user where Mail = :mail", array("mail"=>$mail));
        $data = $query->fetch(); //un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        }
        else {
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["Role"],$data["RegisteredAt"], $data["ID"]);
        }
    }

    public static function getUserById($id) {
        $query = self::execute("SELECT * FROM user where ID = :id", ["id"=>$id]);
        $data = $query->fetch(); 
        if ($query->rowCount() == 0){
            return false;
        } 
        else {
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["Role"],$data["RegisteredAt"], $data["ID"]);
        }
    }

    public static function validateFullname($fullname) {
        return strlen($fullname) >= 3 ? [] : ["Full name should contains at least 3 characters"];
    }

    public static function validatePassword($password) {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        } 
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validateMail($mail) {
        $errors = [];
        if(!filter_var($mail, FILTER_VALIDATE_EMAIL))
            $errors[] = "Email is not a valid email address";
        return $errors;
    }

    public static function validateUnicity($mail) {
        $errors = [];
        $user= self::getUserByMail($mail);
        if ($user) {
            $errors[] = "This user already exists.";
        } 
        return $errors;
    }

    private static function checkPassword($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }

    public static function validatePasswords($password, $password_confirm){
        $errors = User::validatePassword($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public static function validateLogin($mail,$password) {
        $errors = [];
        $user = User::getUserByMail($mail);
        if ($user) {
            if (!self::checkPassword($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the email '$mail'. Please sign up.";
        }
        return $errors;
    }

    //********Methode d'instance *********//

    public function getCardsAuthor() {
        $query = self::execute("Select * from Card where Author = :id ", array("id"=>$this->id));
        $data = $query-> fetchAll();
        $cards = [];
        foreach($data as $row) {
            $cards[] =new Card($row ['Title'], $row['Body'], $row['Position'], $row["CreatedAt"], $row["ModifiedAt"], $row["Author"], $row["Column"],$row["DueDate"],$row["ID"]);;
        }
        return $cards;
    }

    public function getCardsParticipate() {

        $query = self::execute("select * from participate,card where card.id = card and Participant = :id  ", array("id"=>$this->id));
        $data = $query->fetchAll();
        $participants = [];
        foreach($data as $row) {
            $participants[] = new Card($row ['Title'], $row['Body'], $row['Position'], $row["CreatedAt"], $row["ModifiedAt"], $row["Author"], $row["Column"],$row["DueDate"],$row["ID"]);;
        }
        return $participants;
    }

    public function getBoardsCollaborate() {
        $query = self::execute("select * from collaborate,board where board.id= board and Collaborator = :id  ", array("id"=>$this->id));
        $data = $query->fetchAll();
        $collaborators = [];
        foreach($data as $row) {
            $collaborators[] = new Board($row ['Title'],$row['Owner'],$row["CreatedAt"],$row["ModifiedAt"], $row ['ID']);;
        }
        return $collaborators;
    }

    public function getBoardsAuthor() {
        $query = self::execute("select * from board where Owner = :id order by coalesce (modifiedAt, createdAt) DESC", array("id" => $this->id));
        $data = $query->fetchAll();
        $boards = [];
        foreach ($data as $row) {
            
            $boards[] = new Board( $row['Title'],$row['Owner'], $row['CreatedAt'], $row['ModifiedAt'],  $row["ID"]);
            
        }
        
        return $boards;
    }
    public function getBoards() {
        $query = self::execute("select * from board  ", array());
        $data = $query->fetchAll();
        $boards = [];
        $colors = ['#000000','#FF0000','#00FF00','#0000FF','#FFFF00','#00FFFF','#FF00FF','#C0C0C0'];
        shuffle($colors);
        $cpt = 0;
        foreach ($data as $row) {
            $board = new Board( $row['Title'],$row['Owner'], $row['CreatedAt'], $row['ModifiedAt'],  $row["ID"]);
            $board->color = $colors[$cpt];
            ++$cpt;
            $boards[] = $board;
        }
        
        return $boards;
    }

    public function update() {
        if(self::getUserByMail($this->mail)){
            self::execute("UPDATE user SET Password=:Password, FullName=:FullName, WHERE Mail=:Mail ", 
                          array("Mail"=>$this->mail, "FullName"=>$this->fullname, "Password"=>$this->hashed_password));
        }
        else{
            self::execute("INSERT INTO user (Mail,FullName,Password,Role) VALUES(:Mail, :FullName, :Password,:Role)", 
                          array("Mail"=>$this->mail, "FullName"=>$this->fullname, "Password"=>$this->hashed_password,"Role"=>$this->role));
          $this->id=$this->lastInsertId();
        }
        return $this;
    }

    public function delete() {

    }

    public function getOtherBoards() {
        return Board::getOtherBoards($this);
    }

}
