<?php

require_once "framework/Model.php";
require_once "User.php";
require_once "Card.php";


class Participate extends Model {
    public $Participant;
    public $Card;


    public function __construct($Participant,$Card) {
        $this->Participant = $Participant;
        $this->Card = $Card;
    }

    public function add(){
        self::execute('INSERT INTO participate (Participant, Card ) VALUES (:Participant, :Card)', array(
            'Participant' => $this->Participant,
            'Card' => $this->Card,

        ));
        return $this;
    }

    public function delete(){
        self::execute('DELETE FROM participate WHERE Participant = :Participant and Card = :Card', array('Participant' => $this->Participant, 'Card' => $this->Card));
        return $this;
    }

    public static function get($Participant,$Card) {
        $query = self::execute("select * from participate where Participant = :Participant and Card = :Card", array('Participant' => $Participant, 'Card' => $Card));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Participate($row['Participant'],$row['Card']);
        }
    }


}