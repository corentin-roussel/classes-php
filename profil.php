<?php
    include "User.php";

    echo $user->getAllInfos() . "</br>" ;

    echo $user->getLogin() . "</br>";
    echo $user->getEmail() . "</br>";
    echo $user->getLastname() . "</br>";
    echo $user->getFirstname() . "</br>";

    if(!empty($_POST)) {
        extract($_POST);

        if(isset($_POST['modify'])) {
            $login = htmlspecialchars(trim($login));
            $email = htmlspecialchars(trim($email));
            $newmdp = htmlspecialchars(trim($newmdp));
            $confmdp = htmlspecialchars(trim($confmdp));
            $oldmp = htmlspecialchars(trim($oldmdp));

            $user->update($login, $email, $newmdp, $confmdp, $oldmdp);
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
        <main>
            <form action="" method="POST">
                <label for="login">Login :</label>
                <input type="text" name="login" id="login" value="<?php echo $_SESSION['login'] ?>">
                
                <!-- <label for="lastname">Nom :</label>
                <input type="text" name="lastname" id="lastname" value="<?php echo $_SESSION['lastname'] ?>">

                <label for="firstname">Prénom :</label>
                <input type="text" name="firstname" id="firstname" value="<?php echo $_SESSION['firstname'] ?>"> -->
                
                <label for="email">E-mail :</label>
                <input type="email" name="email" id="email" value="<?php echo $_SESSION['email'] ?>">

                <label for="newmdp">Nouveau mot de passe :</label>
                <input type="password" name="newmdp" id="newmdp">

                <label for="confmdp">Confirmation du mot de passe :</label>
                <input type="password" name="confmdp" id="confmdp">

                <label for="oldmdp">Vieux mot de passe :</label>
                <input type="password" name="oldmdp" id="oldmdp">

                <input type="submit" name="modify" value="Modifier">
            </form>
        </main>
    </body>
</html>