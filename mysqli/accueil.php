<?php
    include("User.php");

    require_once "header.php";


    if(isset($_POST['disconnect'])) {
        $user->disconnect();
    }
    
    if(isset($_POST['delete'])) {
        $user->delete();
    }



?>

<form action="" method="POST">
    <?php if(isset($_SESSION['user']->login)) { ?>
    <form action="" method="POST">
    <input type="submit" name="disconnect" value="Déconnexion">

    <input type="submit" name="delete" value="Supprimer votre compte">
    </form>
    <?php }  ?>
</form>

<?php
    if($user->isConnected()) {
        echo " Bienvenue " .$_SESSION['user']->login . ".";
    }else {
        echo "Bonjour tout le monde.";
    } 
?>