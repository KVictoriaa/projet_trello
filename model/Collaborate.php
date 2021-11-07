
<?php

require_once "framework/Model.php";
require_once "User.php";
require_once "board.php";


class Collaborate extends Model {
    public $Collaborator;
    public $Board;


    public function __construct($Board,$Collaborator) {
        $this->Board = $Board;
        $this->Collaborator = $Collaborator;
        
    }

    public function add() {
        self::execute('INSERT INTO collaborate (Board,Collaborator ) VALUES (:Board,:Collaborator)', array(
           'Board' => $this->Board, 
           'Collaborator' => $this->Collaborator
            
        ));
        return $this;
    }

    public function delete() {
        self::execute("DELETE FROM collaborate WHERE Board =:Board and Collaborator =:Collaborator ", array(
            'Board' => $this->Board,
            'Collaborator'=> $this->Collaborator
        ));
            return $this;
            
    }
    
    public static function get($Board,$Collaborator) {
        $query = self::execute("select * from collaborate where Collaborator =:Collaborator and Board =:Board", array('Board' =>$Board, "Collaborator" => $Collaborator));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Collaborate($row['Board'],$row['Collaborator']);
        }
    }

    public static function getNbParticipation($Collaborator) {

        $query = self::execute("select Count(*) as 'count' from participate, collaborate ,board where  participate.Participant = :collaborator and board.ID = collaborate.Board", array('collaborator'=> $Collaborator));
        $data = $query-> fetch();

        return $data['count'];
    }
}