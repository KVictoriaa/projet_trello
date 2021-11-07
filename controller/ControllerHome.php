<?php

require_once "framework/View.php";
require_once "framework/Controller.php";
class ControllerHome extends Controller{
    public function index() {
        
        if ($this->user_logged())
            $this->redirect("board");

        (new View("home"))->show();
    }
}