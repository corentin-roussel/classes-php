<?php
    require_once('_db/connect.php');

    class User {
        private $connect;
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;


        public function __construct() {
            $this->connect = mysqli_connect('localhost', 'root', '', 'classes');
        }

        public function checkpassword ($mdp) {
            $regex = "^\S*(?=\S{5,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$^";
            $valid = FALSE;

                if(preg_match($regex, $mdp)) { 
                    $valid = TRUE; 
                }
            return $valid;
            }

        public function register($login, $mdp, $email, $firstname, $lastname) {
            global $connect;
            $valid = TRUE;

            if(grapheme_strlen($login) < 5) { 
                $valid = FALSE; 
            }
            else if(grapheme_strlen($login) > 25) { 
                $valid = FALSE;
            }
            else {
                $user = ("SELECT login FROM utilisateurs WHERE login = '$login'"); 
                $verif = mysqli_query($this->connect, $user); 

                
                if(mysqli_num_rows($verif) > 0) { 
                    $valid = FALSE;
                    echo "Le login est déja pris";
                }
            }
            
            if($valid) { 
                $req = $this->connect->query ("INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES ('$login', '$mdp', '$email', '$firstname', '$lastname')");
                echo "Enregistrement réussi";
                header("location: connexion.php");
            }
        }

        public function connect($login, $mdp) {
            $req = ("SELECT * FROM utilisateurs WHERE login = '$login'");
            $req = mysqli_query($this->connect, $req);


            if(mysqli_num_rows($req) == 1) {
                $info = mysqli_fetch_assoc($req);
                if(password_verify($mdp, $info['password'])) {
                    $_SESSION['id'] = $info['id'];
                    $_SESSION['login'] = $info['login'];
                    $_SESSION['firstname'] = $info['firstname'];
                    $_SESSION['lastname'] = $info['lastname'];
                    $_SESSION['password'] = $info['password'];
                    $_SESSION['email'] = $info['email'];

                    header("location: accueil.php");
                }else {
                    return "L'identifiant ou le mot de passe est incorrect";
                }
            }else {
                return "L'identifiant ou le mot de passe est incorrect";
            }

        }

        public function disconnect(){
            session_destroy();
            header("location: connexion.php");
        }

        public function isConnected() {
            if($_SESSION['login']) {
                return true;
            }
        }
    }

    $user = new User();
?>