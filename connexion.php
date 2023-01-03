<?php
    require_once("_db/connect.php");
    require_once("User.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion</title>
</head>
<body>
    <main>
        <form action="" method="POST">
            <label for="login">Login :</label>
            <input type="text" name="login" id="login">

            <label for="mdp">Mot de passe :</label>
            <input type="password" name="mdp" id="mdp">

            <input type="submit" name="inscription" value="Se connecter"></br>

            <?php
                if(!empty($_POST)) {
                    extract($_POST);

                    if(isset($_POST['inscription'])) {
                        $login = htmlspecialchars(trim($login));
                        $mdp = htmlspecialchars(trim($mdp));

                        echo($user->connect($login, $mdp));
                    }
                    var_dump($_SESSION);
                }
            ?>
        </form>
    </main>
</body>
</html>