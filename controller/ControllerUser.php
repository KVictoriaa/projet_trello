<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
 
class ControllerUser extends Controller{

    public function index() {
        $user = $this->role;
        if ($this->user_logged())
            $this->redirect("board");
        $this->redirect("user", "login");
    }

    public function login() {
        $user = $this->get_user_or_false();
        if ($user)
            $this->redirect();
        $mail = '';
        $password = '';
        $errors = [];
        if (isset($_POST['mail']) && isset($_POST['password'])) { //note : pourraient contenir des chaînes vides
            $mail = $_POST['mail'];
            $password = $_POST['password'];

            $errors = User::validateLogin($mail, $password);
            if (empty($errors)) {
                $this->log_user(User::getUserByMail($mail),"board");
            }
        }
        (new View("login"))->show(array("mail" => $mail, "password" => $password, "errors" => $errors));
    }

    public function signup() {
        $user = $this->get_user_or_false();
        if ($user)
            $this->redirect();
        $mail = '';
        $fullname='';
        $password = '';
        $passwordconfirm = '';
        $errors = [];

        if (isset($_POST['mail']) && isset($_POST['fullname']) && isset($_POST['password']) && isset($_POST['passwordconfirm'])) {
            $mail = trim($_POST['mail']);
            $fullname= $_POST['fullname'];
            $password = $_POST['password'];
            $role = "user";
            $passwordconfirm = $_POST['passwordconfirm'];
            
            

            $user = new User($mail, $fullname,Tools::my_hash($nassword),$role);
            $errors = User::validateMail($mail);
            $errors = array_merge($errors, User::validateFullname($fullname));
            $errors = array_merge($errors, User::validateUnicity($mail));
            $errors = array_merge($errors, User::validatePasswords($password, $passwordconfirm));
            if (count($errors) == 0) { 
                $user->update(); //sauve l'utilisateur
                $this->log_user($user,"board");
            }
        }
        (new View("signup"))->show(array("mail" => $mail,"fullname"=>$fullname, "password" => $password, 
                                         "passwordconfirm" => $passwordconfirm, "errors" => $errors));
    }

    /*********** Methode JS *********/
    public function emailIsAvalaible() {
    $res="true";
        if(isset($_POST['mail']) && $_POST['mail'] != ""){
            $Mail = $_POST["mail"];
            $user= User::getUserByMail($Mail);
            if($user) {
                $res = "false";
            }
            
        }
        echo $res;
    }
    
    public function confirmEmail() {
        $res="false";
        if(isset($_POST["mail"]) && $_POST["mail"] != "") {
            $Mail = $_POST["mail"];
            $user= User::getUserByMail($Mail);
            if($user) {
                $res = "true";
            }
                
        }
        echo $res;
    }

    public function passwordconfirm() {
        $res="false";
        if(isset($_POST['password']) && $_POST['password'] != "" && isset($_POST["mail"]) && $_POST["mail"] != "") {
            $hashed_password= Tools::my_hash($_POST['password']);
            $user= User::getUserByMail($_POST["mail"]);
            if($user->hashed_password == $hashed_password) {
                $res="true";
            }
        }
        echo $res;
    }
}