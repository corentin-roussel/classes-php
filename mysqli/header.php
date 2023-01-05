<header>
<?php 
    if(isset($_SESSION['user'])) {

?>
    <a href="accueil.php">Accueil</a>
    <a href="profil.php">Profil</a>

<?php 
} else {
?>
    <a href="accueil.php">Accueil</a>
    <a href="connexion.php">Connexion</a>
    <a href="inscription.php">Inscription</a>
<?php
}
?>
</header>