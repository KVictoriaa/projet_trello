<?php

require_once "framework/Model.php";
require_once "User.php";

class Board extends Model {

    public $id;
    public $title;
    public $owner;
    public $created_at;
    public $modified_at;

    public function __construct( $title,$owner, $created_at=null, $modified_at=null, $id=-1) {
        $this->title = $title;
        $this->owner = $owner;
        $this->created_at = $created_at;
        $this->modified_at = $modified_at;
        $this->id = $id;
    }

//*******Methode Static *********//

    public static function getBoardByTitle($title,$owner) {
        $query = self::execute("select * from board where Title = :title and Owner=:owner", array("title" => $title, "owner" =>$owner));
        $data = $query->fetchAll();
        $boards = [];
        foreach ($data as $row) {
            $boards[] = new Board( $row['Title'],($row['Owner']), $row['CreatedAt'], $row['ModifiedAt'],  $row["ID"]);
            
        }
        return $boards;
    }

/*** methode qui regroupe les méthode style getById ou title etc *********/
    public static function get($criteria) {
        $data = ['cle' => key($criteria), 'valeur' => $criteria[key($criteria)]];
        $cle = $data['cle'];
    
        $query = self::execute("SELECT * FROM board where $cle = :valeur", array("valeur"=>$data['valeur']));
        $data = $query->fetch();
    
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Board($data['Title'], $data['Owner'], $data["CreatedAt"], $data["ModifiedAt"], $data["ID"]);;
        }
    }

    public static function validateTitle($title) {
        return strlen($title) >= 3 ? [] : ["Board title should contains at least 3 characters"];
    }

    public static function validateUnicity($title) {
        $query = self::execute("select count(*) from board where Title=:title ", array("title" => $title));
         
        $data = $query->fetch();
        return $data[0] == 0 ? [] : ["Title already exist"];
    }

    //******** Methode d'instance ***********/

    public function getColumns() {
        $query = self::execute("select * from `column` where Board =:id order by position ", array("id"=>$this->id));
        $data = $query-> fetchAll();
        $columns = [];
        foreach($data as $row) {
            $columns[] = new Column($row ['Title'],$row['Position'],$row["CreatedAt"],$row["Board"],$row["ModifiedAt"],$row["ID"]);
        }
        return $columns;
    }

    public function getOwner() {
        $query = self::execute("select * from user where ID = :owner", array("owner"=>$this->owner));
        $data = $query-> fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["Mail"], $data["FullName"], $data["Password"], $data["Role"],$data["RegisteredAt"], $data["ID"]);
        }
        var_dump($this->owner);
    }

    public function getCollaborators() {
        $query = self::execute("select user.* from collaborate, user where Collaborator = user.ID and Board = :ID and user.ID <> :owner", array("ID"=>$this->id, "owner"=>$this->getOwner()->id));
        $data = $query->fetchAll();
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row["Mail"], $row["FullName"], $row["Password"],$row["Role"],$row["RegisteredAt"], $row["ID"]);
        }
    
        return $users;
    }

    public function getNoCollaborator() {
        $cols = $this->getCollaborators();
        $query = self::execute("select * from user where user.ID <>:owner ", array("owner"=>$this->owner));
        $data = $query->fetchAll();
        $users = [];
        foreach ($data as $row) {
                $user = new User($row["Mail"], $row["FullName"], $row["Password"],$row["Role"],$row["RegisteredAt"], $row["ID"]);
                if(!in_array($user, $cols) && $user->id != $this->owner) {
                    $users[] = $user;
                }
        }
        return $users;
    }

    public static function getOtherBoards($user) {
        $query = self::execute("select * from board where Owner != :id", array("id"=>$user->id));
        $data = $query-> fetchAll();
        $boards=[];
        foreach($data as $row){
            $board = new Board($row ['Title'],$row['Owner'],$row["CreatedAt"],$row["ModifiedAt"], $row ['ID']);
            if(!in_array($board, $user->getBoardsCollaborate()))
                $boards[]= $board;
        }

        return $boards;
    }

    public function getBoardsOwner() {

    }

    public function getBoardsCollaborate() {

    }

    public function update() {
        if(self::get(["id" => $this->id]))
        self::execute("UPDATE board SET Title=:Title,Owner=:Owner,ModifiedAt=:ModifiedAt  WHERE ID=:id ", 
              array("Title"=>$this->title,"Owner"=>$this->owner,"ModifiedAt"=>date('Y-m-d H:i:s'),"id"=>$this->id));
        else
            self::execute("INSERT INTO board(Title,Owner,ModifiedAt) VALUES(:Title,:Owner,:ModifiedAt)", 
                   array("Title"=>$this->title,"Owner"=>$this->owner,"ModifiedAt"=>$this->modified_at));
        return $this;
    }

    public function delete() {
        self::execute("Delete from collaborate where Board =:board",array('board'=>$this->id));

        //if ($this->getOwner()->id == User::get_current_user()->ID) {
            self::execute('DELETE FROM board WHERE ID = :id', array('id' => $this->id));
            
        //}
        return false;
    }
    /****** Recupère le nombre de colonne dans le board *******/
    public function getNumberColumn() {
        $query = self::execute("select COUNT(*) as 'count' from`column` where Board = :id ", array("id" => $this->id));
        $data = $query-> fetch();

        return $data['count'];
    }


}
