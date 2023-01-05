<?php
    session_start();



    class User {
        private $connect;
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;
        public $mdp;


        public function __construct($id, $login, $mdp, $email, $firstname, $lastname) {
            $this->connect = mysqli_connect('localhost', 'root', '', 'classes');

            $this->id = $id;
            $this->login = $login;
            $this->mdp = $mdp;
            $this->email = $email;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
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
            $valid = TRUE;

            if(grapheme_strlen($login) < 5) { 
                $valid = FALSE; 
                $msg_register = "Le login ne doit pas contenir moins de 5 caractéres";
            }
            else if(grapheme_strlen($login) > 25) { 
                $valid = FALSE;
                $msg_register = "Le login ne doit pas contenir plus de 25 caractéres";
            }
            else {
                $user = ("SELECT login FROM utilisateurs WHERE login = '$login'"); 
                $verif = mysqli_query($this->connect, $user); 

                
                if(mysqli_num_rows($verif) > 0) { 
                    $valid = FALSE;
                    $msg_register = "Le login est déja pris";
                }
            }
            
            if($valid) { 
                $req = $this->connect->query ("INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES ('$login', '$mdp', '$email', '$firstname', '$lastname')");
                header("location: connexion.php");

            }
        }

        public function connect($login, $mdp) {
            $req = ("SELECT * FROM utilisateurs WHERE login = '$login'");
            $req = mysqli_query($this->connect, $req);


            if(mysqli_num_rows($req) == 1) {
                $info = mysqli_fetch_assoc($req);
                if(password_verify($mdp, $info['password'])) {
                    $user = new User($info['id'], $info['login'], $info['password'], $info['email'], $info['lastname'], $info['firstname']);
                    $_SESSION['user'] =  $user;


                    // $_SESSION['id'] = $info['id'];
                    // $_SESSION['login'] = $info['login'];
                    // $_SESSION['firstname'] = $info['firstname'];
                    // $_SESSION['lastname'] = $info['lastname'];
                    // $_SESSION['password'] = $info['password'];
                    // $_SESSION['email'] = $info['email'];
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
            if($_SESSION) {
                return true;
            }
        }

        public function delete() {
            $sessionID = $_SESSION['user']->id;

            $del = $this->connect->query ("DELETE FROM `utilisateurs` WHERE id = $sessionID ");
            $this->disconnect();
        }        

        public function update($login, $email, $newmdp, $confmdp, $oldmdp) {
            $valid = TRUE;
            $sessionLogin = $_SESSION['user']->login;

            if(password_verify($oldmdp, $_SESSION['user']->mdp)) {

                if($login != $_SESSION['user']->login) {
                    $req = ("SELECT login FROM utilisateurs WHERE login = '$login'");
                    $verif = mysqli_query($this->connect, $req);

                    if(mysqli_num_rows($verif) > 0) {
                        $valid = FALSE;
                        $err_login = "Ce login est déja pris";
                    }

                }else if($login == $_SESSION['user']->login) {
                    $valid = FALSE;
                    $err_login = "Le login $login est déja pris.";
                }else if(grapheme_strlen($login) < 5) { 
                    $valid = FALSE;
                    $err_login = "Le login doit contenir au minimum 5 caractéres.";
                }
                else if(grapheme_strlen($login) > 25) { 
                    $valid = FALSE; 
                    $err_login = "Le login doit contenir maximum 25 caractéres."; 
                }

                if($email != $_SESSION['user']->email) {
                    $req = ("SELECT email FROM utilisateurs WHERE email = '$email'");
                    $verif = mysqli_query($this->connect, $req);

                    if(mysqli_num_rows($verif) > 0) {
                        $valid = FALSE;
                        $err_mail = "Le mail est déja pris";
                    }
                }

                if($newmdp != $confmdp) {
                    $valid = FALSE;
                    return "La confirmation du mot de passe n'est pas bonne";
                    
                }else if($this->checkpassword($newmdp)) {
                    $crypt_password = password_hash($newmdp, PASSWORD_DEFAULT);
                }else {
                    $valid = FALSE;
                    return "Le mot de passe doit contenir cinq carcatéres minimum avec au moins une majuscule, une minuscule, un chiffre et un caractére spéciale";
                }

                if($valid) {
                    $req = ("UPDATE utilisateurs SET login = '$login', password = '$crypt_password', email = '$email' WHERE login = '$sessionLogin'");
                    $change = mysqli_query($this->connect, $req);

                    $this->disconnect();

                    return "changement réussi";

                }
                

            }else {
                $valid = FALSE;
                return "L'ancien mot de passe n'est pas correct";
            }

            
        }

        public function getAllInfos() {
            $this->connect;

            $req = mysqli_query($this->connect, "SELECT * FROM `utilisateurs` WHERE id = {$_SESSION['user']->id} ");
            $req = mysqli_fetch_assoc($req);

            return  <<<HTML
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Login</th>
                                    <th>E-mail</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{$req['id']}</td>
                                    <td>{$req['login']}</td>
                                    <td>{$req['email']}</td>
                                    <td>{$req['firstname']}</td>
                                    <td>{$req['lastname']}</td>
                                </tr>
                            </tbody>
                        </table>
                    HTML;
        }

        public function getLogin() {
            $this->connect;

            $req = mysqli_query($this->connect, "SELECT * FROM `utilisateurs` WHERE id = {$_SESSION['user']->id} ");
            $req = mysqli_fetch_assoc($req);

            return "Le login est : " . $req['login'];
        }

        public function getEmail() {
            $this->connect;

            $req = mysqli_query($this->connect, "SELECT * FROM `utilisateurs` WHERE id = {$_SESSION['user']->id} ");
            $req = mysqli_fetch_assoc($req);

            return "L'e-mail est : " . $req['email'];
        }

        public function getFirstname() {
            $this->connect;

            $req = mysqli_query($this->connect, "SELECT * FROM `utilisateurs` WHERE id = {$_SESSION['user']->id} ");
            $req = mysqli_fetch_assoc($req);

            return "Le prénom est : " . $req['firstname'];
        }

        public function getLastname() {
            $this->connect;

            $req = mysqli_query($this->connect, "SELECT * FROM `utilisateurs` WHERE id = {$_SESSION['user']->id} ");
            $req = mysqli_fetch_assoc($req);

            return "Le nom est : " .$req['lastname'];
        }
        
    }
    $user = new User($id = NULL, $login = NULL, $mdp = NULL, $email = NULL, $firstname = NULL, $lastname = NULL);
?>