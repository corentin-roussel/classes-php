<?php
    require_once("_db/connect.php");
    require_once("User.php");

    if($user->isConnected()) {
        echo " Bienvenue " .$_SESSION['login'] . ".";
    }else {
        header("location : connexion.php");
    }

    if($_POST['disconnect']) {
        $user->disconnect();
    }
?>

<form action="" method="POST">
    <input type="submit" name="disconnect" value="Déconnexion">
</form>