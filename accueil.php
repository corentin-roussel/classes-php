<?php
    include("User.php");

    if(isset($_POST['disconnect'])) {
        $user->disconnect();
    }

    if($user->isConnected()) {
        echo " Bienvenue " .$_SESSION['login'] . ".";
    }

    if(isset($_POST['delete'])) {
        $user->delete();
    }

    //var_dump($_SESSION);
?>
<form action="" method="POST">
    <input type="submit" name="disconnect" value="Déconnexion">

    <input type="submit" name="delete" value="Supprimer votre compte">
</form>