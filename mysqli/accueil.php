<?php
    include("User.php");

    require_once "header.php";


    if(isset($_POST['disconnect'])) {
        $user->disconnect();
    }
    
    if(isset($_POST['delete'])) {
        $user->delete();
    }

    if($user->isConnected()) {
        echo " Bienvenue " .$_SESSION['user']->login . ".";
    }



    //var_dump($_SESSION);

?>

<form action="" method="POST">
    <input type="submit" name="disconnect" value="Déconnexion">

    <input type="submit" name="delete" value="Supprimer votre compte">
</form>