<?php
$title = "Profil";
require_once 'header.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Récupérez les informations de l'utilisateur depuis la session
$nom = $_SESSION['nom_user'] ?? 'Nom';
$prenom = $_SESSION['prenom_user'] ?? 'Prénom';
$photo = $_SESSION['photo_user'] ?? 'default.png'; // Image par défaut si aucune photo n'est disponible
?>
<div class="img_profil">
    <img src="uploads/<?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>" alt="image de profil">
    <h2><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email_user'], ENT_QUOTES, 'UTF-8') ?></p>
    
    <a href="logout.php">Se déconnecter</a>
</div>


<?php
require_once 'footer.php';
?>