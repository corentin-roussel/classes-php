<?php

    session_start();

    class Userpdo {
        private $connect;
        private $err_login = '';
        private $err_email = '';
        private $err_mdp = '';
        private $id;
        public $login;
        public $email;
        public $firstname;
        public $lastname;
        public $mdp;


        public function __construct($id, $login, $mdp, $email, $firstname, $lastname) {
            $this->connect = new PDO('mysql:host=localhost;dbname=classes', 'root', '');

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

            if(empty($login)){
                $valid = FALSE;
                $this->err_login = "Le login ne peut pas être vide";
            }
            else if(grapheme_strlen($login) < 5) { 
                $valid = FALSE; 
                $this->err_login = "Le login ne doit pas contenir moins de 5 caractéres";
            }
            else if(grapheme_strlen($login) > 25) { 
                $valid = FALSE;
                $this->err_login = "Le login ne doit pas contenir plus de 25 caractéres";
            }
            else {
                $user = $this->connect->prepare("SELECT login FROM utilisateurs WHERE login = ?"); 
                $user->execute([$login]);
                $verif = $user->rowCount(); 

                
                if($verif > 0) { 
                    $valid = FALSE;
                    $this->err_login = "Le login est déja pris";
                }
            }


            if(empty($email)) {
                $valid = FALSE;
                $this->err_email = "Le mail ne peut pas être vide";
            }
            else {
                $user = $this->connect->prepare("SELECT email FROM utilisateurs WHERE email = ?"); 
                $user->execute([$email]);
                $verif = $user->rowCount(); 

                if($verif > 0) {
                    $valid = FALSE;
                    $this->err_email = "Le mail est déja pris";
                }
            }
            
            if($valid) { 
                $req = $this->connect->prepare ("INSERT INTO `utilisateurs` (`login`, `password`, `email`, `firstname`, `lastname`) VALUES (?, ?, ?, ?, ?)");
                $req->execute(array($login, $mdp, $email, $firstname, $lastname));
                header("location: connexion.php");

            }
        }

        public function connect($login, $mdp) {
            $user = $this->connect->prepare("SELECT * FROM utilisateurs WHERE login = ?"); 
            $user->execute([$login]);
            $verif = $user->rowCount(); 


            if($verif == 1) {
                $info = $user->fetch(PDO::FETCH_ASSOC);
                if(password_verify($mdp, $info['password'])) {
                    
                    // $user = new Userpdo($info['id'], $info['login'], $info['password'], $info['email'], $info['lastname'], $info['firstname']);
                    // $_SESSION['user'] =  $user;

                    

                    $_SESSION['id'] = $info['id'];
                    $_SESSION['login'] = $info['login'];
                    $_SESSION['firstname'] = $info['firstname'];
                    $_SESSION['lastname'] = $info['lastname'];
                    $_SESSION['password'] = $info['password'];
                    $_SESSION['email'] = $info['email'];

                    header("location: accueil.php");


                }else {
                    $this->err_mdp = "L'identifiant ou le mot de passe est incorrect";
                }
            }else {
                $this->err_mdp = "L'identifiant ou le mot de passe est incorrect";
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
            $sessionID = $_SESSION['id'];

            $del = $this->connect->prepare ("DELETE FROM `utilisateurs` WHERE id = ? ");
            $del->execute([$sessionID]);
            $this->disconnect();
            header("location: inscription.php");
        }        

        public function update($login, $email, $newmdp, $confmdp, $oldmdp) {
            $valid = TRUE;
            
            $sessionLogin = $_SESSION['login'];

            if(password_verify($oldmdp, $_SESSION['password'])) {

                if($login != $_SESSION['login']) {
                    $user = $this->connect->prepare("SELECT * FROM utilisateurs WHERE login = ?"); 
                    $user->execute([$login]);
                    $verif = $user->rowCount(); 

                    if($verif > 0) {
                        $valid = FALSE;
                        $this->err_login = "Ce login est déja pris";
                    }

                }else if($login === $_SESSION['login']) {
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

                if($email != $_SESSION['email']) {
                    $user = $this->connect->prepare("SELECT email FROM utilisateurs WHERE email = ?"); 
                    $user->execute([$email]);
                    $verif = $user->rowCount(); 

                    if($verif > 0) {
                        $valid = FALSE;
                        $err_mail = "Le mail est déja pris";
                    }
                }

                if($newmdp != $confmdp) {
                    $valid = FALSE;
                    $this->err_mdp = "La confirmation du mot de passe n'est pas bonne";
                    
                }else if($this->checkpassword($newmdp)) {
                    $crypt_password = password_hash($newmdp, PASSWORD_DEFAULT);
                }else {
                    $valid = FALSE;
                    $this->err_mdp = "Le mot de passe doit contenir cinq carcatéres minimum avec au moins une majuscule, une minuscule, un chiffre et un caractére spéciale";
                }

                if($valid) {
                    $req = $this->connect->prepare("UPDATE utilisateurs SET login = ?, password = ?, email = ? WHERE login = ?");
                    $change = $req->execute(array($login, $crypt_password, $email, $sessionLogin));

                    $this->disconnect();
                }
                

            }else {
                $valid = FALSE;
                $this->err_mdp = "L'ancien mot de passe n'est pas correct";
            }

            
        }

        public function getAllInfos() {
            $this->connect;
            

            $sessionID = $_SESSION['id'];

            $req = $this->connect->prepare("SELECT * FROM `utilisateurs` WHERE id = ? ");
            $req->execute([$sessionID]);
            $request = $req->fetch(PDO::FETCH_ASSOC);


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
                                    <td>{$request['id']}</td>
                                    <td>{$request['login']}</td>
                                    <td>{$request['email']}</td>
                                    <td>{$request['firstname']}</td>
                                    <td>{$request['lastname']}</td>
                                </tr>
                            </tbody>
                        </table>
                    HTML;
        }

        public function getLogin() {
            $this->connect;

            $req = $this->connect->prepare("SELECT * FROM `utilisateurs` WHERE id = ? ");
            $req->execute([$_SESSION['id']]);
            $request = $req->fetch(PDO::FETCH_ASSOC);

            return "Le login est : " . $request['login'];
        }

        public function getEmail() {
            $this->connect;

            $req = $this->connect->prepare("SELECT * FROM `utilisateurs` WHERE id = ? ");
            $req->execute([$_SESSION['id']]);
            $request = $req->fetch(PDO::FETCH_ASSOC);

            return "L'e-mail est : " . $request['email'];
        }

        public function getFirstname() {
            $this->connect;

            $req = $this->connect->prepare("SELECT * FROM `utilisateurs` WHERE id = ? ");
            $req->execute([$_SESSION['id']]);
            $request = $req->fetch(PDO::FETCH_ASSOC);

            return "Le prénom est : " . $request['firstname'];
        }

        public function getLastname() {
            $this->connect;

            $req = $this->connect->prepare("SELECT * FROM `utilisateurs` WHERE id = ? ");
            $req->execute([$_SESSION['id']]);
            $request = $req->fetch(PDO::FETCH_ASSOC);

            return "Le nom est : " .$request['lastname'];
        }

        public function getErrorMdp() {
            return $this->err_mdp;
        }

        public function getErrorEmail() {
            return $this->err_email;
        }

        public function getErrorLogin() {
            return $this->err_login;
        }
        
    }
    $user = new Userpdo($id = NULL, $login = NULL, $mdp = NULL, $email = NULL, $firstname = NULL, $lastname = NULL);
?>