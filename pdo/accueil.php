<?php
    include ("User-pdo.php");
    require_once "header.php";

    if(isset($_POST['delete'])) {
        $user->delete();
    }

    if(isset($_POST['disconnect'])) {
        $user->disconnect();
    }

    if($user->isConnected()) {
        echo " Bienvenue " .$_SESSION['login'] . ".";
    }else {
        echo "Bonjour tout le monde";
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>
    <header>
        <?php require_once "header.php"; ?>
    </header>
    <main>
        <?php if(isset($_SESSION['login'])) { ?>
        <form action="" method="POST">
        <input type="submit" name="disconnect" value="Déconnexion">

        <input type="submit" name="delete" value="Supprimer votre compte">
        </form>
        <?php }  ?>
    </main>
</body>
</html>
