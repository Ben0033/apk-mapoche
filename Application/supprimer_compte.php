<?php

require_once 'header.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Ici, tu dois supprimer l'utilisateur de la base de données
// Exemple : $id = $_SESSION['id_user']; ... requête SQL DELETE ...

// Détruit la session
session_destroy();
?>
<h2>Compte supprimé</h2>
<p>Votre compte a bien été supprimé.</p>
<a href="connexion.php">Retour à la connexion</a>
<?php require_once 'footer.php'; ?>