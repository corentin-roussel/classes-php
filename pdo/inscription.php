<?php
    include ("User-pdo.php");

    if(!empty($_POST)) {
        extract($_POST);

        if(isset($_POST['inscription'])) {
            $login = htmlspecialchars(trim($login));
            $lastname = htmlspecialchars(trim($lastname));
            $firstname = htmlspecialchars(trim($firstname));
            $email = htmlspecialchars(trim($email));
            $mdp = htmlspecialchars(trim($mdp));
            $confmdp = htmlspecialchars(trim($confmdp));
            
            
            if($mdp === $confmdp) { 
                if($user->checkpassword($mdp) === TRUE) {
                    $crypt_mdp = password_hash($mdp, PASSWORD_DEFAULT);

                    $user->register("$login", "$crypt_mdp", "$email", "$firstname", "$lastname");
                }else {
                    echo "Le mot de passe doit contenir au moins 5 caractéres 1 majuscules, 1 minuscules et 1 carcatére spéciale.";
                }

            }else {
                echo "Le mot de passe et la confirmation ne sont pas identiques";
            }
        }else {
            echo "Les champs doivent être remplis";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription</title>
</head>
<body>
    <?php require_once "header.php"; ?>
    <main>
        <form action="" method="POST">

            <?php echo $user->getErrorLogin(); ?>
            <label for="login">Login :</label>
            <input type="text" name="login" id="login">
            
            <label for="lastname">Nom :</label>
            <input type="text" name="lastname" id="lastname">

            <label for="firstname">Prénom :</label>
            <input type="text" name="firstname" id="firstname">
            
            <?php echo $user->getErrorEmail(); ?>
            <label for="email">E-mail :</label>
            <input type="email" name="email" id="email">

            <label for="mdp">Mot de passe :</label>
            <input type="password" name="mdp" id="mdp">

            <label for="confmdp">Confirmation :</label>
            <input type="password" name="confmdp" id="confmdp">

            <input type="submit" name="inscription" value="S'inscrire">
        </form>
    </main>
</body>
</html>