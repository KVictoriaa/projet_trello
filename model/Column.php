<?php

require_once "framework/Model.php";
require_once "board.php";
require_once "Card.php";

 class Column extends Model{
     public $id;
     public $title;
     public $position;
     public $createdAt;
     public $modifiedAt;
     public $board;

     public function __construct($title,$position,$createdAt,$board ,$modifiedAt=NULL,$id=-1){
        $this->id = $id;
        $this->board = $board;
        $this->title = $title;
        $this->position = $position;
        $this->createdAt= $createdAt;
        $this->modifiedAt = $modifiedAt; 
        
     }

     //***********Methodes static ************//

    public static function getColumnByTitle($title,$boardId) {
        $query = self::execute("SELECT * FROM `column` where Title = :title and Board = :boardId", array("title" => $title, "boardId"=>$boardId));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Column($data ['Title'],$data['Position'],$data["CreatedAt"],$data["Board"],$data["ModifiedAt"],$data["ID"]);;
        }
    }
    /*** methode qui regroupe les méthode style getById ou title etc *********/
    public static function get($criteria) {
        $data = ['cle' => key($criteria), 'valeur' => $criteria[key($criteria)]];
        $cle = $data['cle'];
    
        $query = self::execute("SELECT * FROM `column` where $cle = :valeur", array("valeur" => $data['valeur']));
        $data = $query->fetch();
    
            if ($query->rowCount() == 0) {
                return false;
            } else {
                return new Column($data ['Title'],$data['Position'],$data["CreatedAt"],$data["Board"],$data["ModifiedAt"],$data["ID"]);;
            }
    }

    public static function validateTitle($title) {
        return strlen($title) >= 3 ? [] : ["Column title should contains at least 3 characters"];

    }

    public static function validateUnicity($title,$board) {
        $query = self::execute("select count(*) from `column` where Title=:Title and Board=:Board", array("Title" => $title, "Board" => $board));
         
        $data = $query->fetch();
        return $data[0] == 0 ? [] : ["Title already exist"];

    }
     
    public static function moveColumnJs($startColumnId, $endColumnId) {
        $startColumn = self::get(["id" => $startColumnId]);
        $endColumn = self::get(["id" => $endColumnId]);
        $tmp = $startColumn->position;
        $startColumn->position = $endColumn->position;
        $endColumn->position = $tmp;
        $startColumn->update();
        $endColumn->update();
    }

     //*************Methodes d'instance***********//

    public function getCards() {
        $query = self::execute("select * from card where `Column` = :id order by position  ", array("id"=>$this->id));
        $data = $query->fetchAll();
        $cards = [];
        foreach($data as $row) {
            $cards[] = new Card($row ['Title'], $row['Body'], $row['Position'], $row["CreatedAt"], $row["Author"], $row["Column"], $row["ModifiedAt"],$row["DueDate"] ,$row["ID"]);
            
        }
        return $cards;
    }

    public function getBoard() {
        $query = self::execute("select * from board where id = :board ", array("board" => $this->board));
        $data = $query->fetch();
        if($query->rowCount()==0) {
            return false; 
        } else {
            return  new Board($data['Title'], $data['Owner'], $data["CreatedAt"], $data["ModifiedAt"], $data["ID"]);;
        }
    }

    public function update() {
        if(self::get(["id" => $this->id])) 
            self::execute("UPDATE `column` SET Title=:Title,Position=:Position,Board=:Board,ModifiedAt=:ModifiedAt WHERE ID=:id ", 
                array("Title"=>$this->title, "Position"=>$this->position,"Board"=>$this->board,"ModifiedAt"=>$this->modifiedAt,"id"=>$this->id));
        else
    
            self::execute("INSERT INTO `column` (Title,Position,Board,ModifiedAt) VALUES(:Title,:Position,:Board,:ModifiedAt)", 
                array("Title"=>$this->title, "Position"=>$this->position,"Board"=>$this->board,"ModifiedAt"=>$this->modifiedAt));
        return $this;
        
    }

    public function delete() {
        $board =$this->getBoard();
       
        self::execute('DELETE FROM `column` WHERE ID = :id', array('id' => $this->id));
        for ($k = $this->position; $k < $board->getNumberColumn(); $k++) {
            $column = $this->getByPositionAndBoard($k+1);
            $column->position = $k; 
            $column->update();
        }
        return $this;
    
    }
    public function firstPosition() {
        return $this->position == 0;
     }

    public function lastPosition() {
        $columns = Column::get_c($this->board->id);
        return count($columns) == $this->position +1;
    }
    public function getNumberCard() {
        $query = self::execute("select COUNT(*) as 'count' from card where `Column` = :id ", array("id" => $this->id));
        $data = $query-> fetch();

        return $data['count'];
    }

    public function previous(){
        $query  = self::execute("select * from `column` where Position < :position and Board = :board order by Position desc limit 1",
            ["position" => $this->position, "board" => $this->board]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Column($data ['Title'],$data['Position'],$data["CreatedAt"],$data["Board"],$data["ModifiedAt"],$data["ID"]);;
    }

    public function next(){
        $query  = self::execute("select * from `column` where Position > :position and Board = :board order by Position limit 1",
            ["position" => $this->position, "board" => $this->board]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Column($data ['Title'],$data['Position'],$data["CreatedAt"],$data["Board"],$data["ModifiedAt"],$data["ID"]);;
    }
    public function getByPositionAndBoard($position) {
        $query = self::execute("select * from `column` where Board = :Board and Position = :Position", array("Board" => $this->board, "Position"=>$position));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Column($data ['Title'],$data['Position'],$data["CreatedAt"],$data["Board"],$data["ModifiedAt"],$data["ID"]);;
    
    }
    public function left(){
        $position = $this->position;
        
        $column = $this->getByPositionAndBoard($position-1);
        $this->position=$column->position;
        $column->position = $position;
        $this->update();
        $column->update();
        
      
    }

    public function right(){
        $position = $this->position;
        
        $column = $this->getByPositionAndBoard($position+1);
        $this->position=$column->position;
        $column->position = $position;
        $this->update();
        $column->update();
    }

 }
