<?php

require_once "framework/Model.php";
require_once "board.php";
require_once "Column.php";

class Card extends Model{
    public $id;
    public $column;
    public $author;
    public $title;
    public $body;
    public $position;
    public $createdAt;
    public $modifiedAt;
    public $dueDate;

    public function __construct($title, $body,$position,$createdAt, $author,$column,$modifiedAt=NULL,$dueDate=NULL,$id=-1){
        $this->id = $id;
        $this->column = $column;
        $this->author = $author;
        $this->title = $title;
        $this->body = $body;
        $this->position = $position;
        $this->dueDate=$dueDate;
        $this->createdAt= $createdAt;
        $this->modifiedAt = $modifiedAt
        ;

    }



    //*************Methodes static ****************//
     
    public static function getCardByTitle($title,$column) {
        $query = self::execute("select card.* from card
        join `column` on `Column`.ID = Card.`Column`
        where card.Title = :title and Board=:board",
        array("board" => $column->board, "title" => $title));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);
        }
    }
    public static function get($criteria) {
        $data = ['cle' => key($criteria), 'valeur' => $criteria[key($criteria)]];
        $cle = $data['cle'];
    
        $query = self::execute("SELECT * FROM card where $cle = :valeur", array("valeur" => $data['valeur']));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);;
        }
    }

    public static function validateTitle($title) {
        $errors = array();
        if (!(isset($title) && is_string($title) && strlen($title) > 0)) {
            $errors[] = "title is required.";
        } if (!(isset($title) && is_string($title) && strlen($title) >= 3 )) {
            $errors[] = "title length must be min 3 character .";
        
        } 
        return $errors;
    }

    public static function validateUnicity($title,$column) {
        $newTitle = self::getCardByTitle($title, $column);
        $errors = array();
        if ($newTitle) {
            $errors[] = "This title already exists.";
        } 

        return $errors;
       
    }

    public static function moveCardDragUpDown($sartCardId,$endCardId) {
        $startCard = self::get(["id"=>$sartCardId]);
        $endCard = self::get(["id"=>$endCardId]);
        $tmp = $startCard->position;
        $startCard->position =$endCard->position;
        $endCard->position = $tmp;
        //var_dump($startCard);
        $startCard->update();
        $endCard->update();

    }

    public static function moveCardDragColumn($sartCardColumnId,$sartColumnId,$endColumnId) {
        $startCard = self::get(["id"=>$sartCardColumnId]);
        //$endCard = self::get(["id"=>$endCardColumnId]);
        $startColumn = Column::get(["id"=>$sartColumnId]);
        $endColumn = Column::get(["id"=>$endColumnId]);
        $startCard->position = $endColumn->getNumberCard();
        $startCard->column = $endColumnId;
        $startCard->update();
        $cards = $startColumn->getCards();
        $cpt = 0;
        foreach($cards as $card) {
            $card->position =$cpt;
            $card->update();
            $cpt++;
        }

    }

    //*************Methodes d'instance **************//

    public function getParticipants() {
        $query = self::execute("select user.* from participate ,user where Participant = user.ID and participate.Card =:ID ", array("ID"=>$this->id));
        $data = $query->fetchAll();
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row["Mail"], $row["FullName"], $row["Password"],$row["Role"],$row["RegisteredAt"], $row["ID"]);
        }
        return $users;
    }

    public function noParticipant() {
        $participates = $this->getParticipants();
        $collaborators= $this->getColumn()->getBoard()->getCollaborators();
        $board = $this->getColumn()->getBoard();
        $query = self::execute("select * from user ", array());
        $data = $query->fetchAll();
        $users = [];
        if(!in_array($board->getOwner(), $participates)) {
            $users[] = $board->getOwner();
        }
        foreach ($data as $row) {
            $user = new User($row["Mail"], $row["FullName"], $row["Password"],$row["Role"],$row["RegisteredAt"], $row["ID"]);
            if(!in_array($user, $participates) && in_array($user,$collaborators)) {
                $users[] = $user;
            }
        }
        return $users;
    }

    public function getAuthor() {
        $query = self::execute("select * from user where id = :author  ", array("author" => $this->author));
        $data = $query->fetch();
        if($query->rowCount() == 0) {
            return false; 
        } else {
             return new User($data["Mail"], $data["FullName"], $data["Password"], $data["Role"],$data["RegisteredAt"], $data["ID"]);
        } 
    }

    public function getColumn() {
        $query = self::execute("select * from `column` where id = :column  ", array("column" => $this->column));
        $data = $query->fetch();
        if($query->rowCount() == 0) {
            return false; 
        } else {
            return new Column($data['Title'], $data['Position'], $data["CreatedAt"],  $data["Board"],$data["ModifiedAt"], $data["ID"]);;
        } 
    }

    public function update() {
        $dueDate = $this->dueDate;
        if($dueDate == ""){
            $dueDate = null;
        }
        if(self::get(["id" => $this->id]))
        self::execute("UPDATE card SET Title=:Title,Body=:Body,Position=:Position,Author=:Author,`Column`=:Column,DueDate=:DueDate,ModifiedAt=:ModifiedAt WHERE ID=:id ", 
                array("Title"=>$this->title,"Body"=>$this->body, "Position"=>$this->position,"Author"=>$this->author,"Column"=>$this->column,"ModifiedAt"=>$this->modifiedAt,"DueDate"=>$dueDate,"id"=>$this->id));
        else
            self::execute("INSERT INTO card(Title,Body,Position,Author,`Column`,ModifiedAt,DueDate) VALUES(:Title,:Body,:Position,:Author,:Column,:ModifiedAt,:DueDate)", 
                array("Title"=>$this->title,"Body"=>$this->body, "Position"=>$this->position,"Author"=>$this->author,"Column"=>$this->column,"ModifiedAt"=>$this->modifiedAt,"DueDate"=>$this->dueDate));
        return $this;

    }

    public function delete() {
        
        self::execute('DELETE FROM card WHERE ID = :id', array('id' => $this->id));
        $column = $this->getColumn();
        
        for($k = $this->position; $k <$column->getNumberCard();$k++){
            $card = $this->getByPositionAndColumn($k+1);
            $card->position = $k;
            $card->update();
        }
        return $this;
    }

    public function isDuedate() {
        return strtotime($this->dueDate) <= time();
    }

    public function previous(){
        $query  = self::execute("select * from card where Position < :position and `Column` = :column order by Position desc limit 1",
            ["position" => $this->position, "column" => $this->column]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);;
    }

    public function next(){
        $query  = self::execute("select * from card where Position > :position and `Column` = :column order by Position limit 1",
            ["position" => $this->position, "column" => $this->column]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);;
    }

    public function first($column){
        $query  = self::execute("select * from card where `Column` = :column order by Position limit 1",
            ["column" => $column]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);;
    }

    public function last($column){
        $query  = self::execute("select * from card where `Column` = :column order by Position desc limit 1",
            ["column" => $column]);
        $data = $query->fetch();
        
        if ($query->rowCount() == 0) {
            return null;
        }
        return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);;
    }

    public function getByPositionAndColumn($position) {
        $query = self::execute("SELECT * FROM card where Position = :position and `Column`= :column ", array("position" => $position, "column" => $this->column));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Card($data ['Title'],$data['Body'],$data['Position'],$data["CreatedAt"],$data["Author"],$data["Column"],$data["ModifiedAt"],$data["DueDate"],$data["ID"]);;
        }
    } 
    
    public function down(){
        $position = $this->position;
        $card = $this->getByPositionAndColumn($position+1);
        $this->position=$card->position;
        $card->position = $position;
        $this->update();
        $card->update();
    }

    public function up(){
        $position = $this->position;
        $card = $this->getByPositionAndColumn($position-1);
        $this->position=$card->position;
        $card->position = $position;
        $this->update();
        $card->update();
    }

    public function moveCardLeft(){
        $positionCard = $this->position;
        $column1 = $this->getColumn();
        $column2 = $column1->getByPositionAndBoard($column1->position-1);
        $this->position = $column2->getNumberCard();
        $this->column = $column2->id;
        $this->update();
        $cards = $column1->getCards();

        $cpt = 0;
        foreach($cards as $card) {
            $card->position =$cpt;
            $card->update();
            $cpt++;
        }
    }

    public function moveCardRight() {
        $positionCard = $this->position;
        $column1 = $this->getColumn();
        $column2 = $column1->getByPositionAndBoard($column1->position+1);
        $this->position = $column2->getNumberCard();
        $this->column = $column2->id;
        $this->update();
        $cards = $column1->getCards();

        $cpt = 0;
        foreach($cards as $card) {
            $card->position =$cpt;
            $card->update();
            $cpt++;
        }
        
    }

    

}
