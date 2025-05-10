<?php 
session_start();
$title = isset($title) ? $title : "MaPoche"; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title><?="MaPoche-".$title?></title>
</head>
<body>
    <header>
        <div class="hgauche">
        <img src="images/wallet.jpg" alt="logo">
        </div>
        <nav class="hdroite">
            <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="historique.php">Historique</a></li>
            <li><a href="profil.php">Profil</a></li>
            <?php if (!isset($_SESSION['id_user'])): ?>
                <li><a href="connexion.php">Connexion</a></li>
            <?php endif; ?>
            </ul>
        </nav>
    </header>