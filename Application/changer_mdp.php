<?php

require_once 'header.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancien = $_POST['ancien'] ?? '';
    $nouveau = $_POST['nouveau'] ?? '';
    $confirmer = $_POST['confirmer'] ?? '';

    // À adapter : vérifier l'ancien mot de passe en base de données
    // Ici, on suppose que le mot de passe est "demo" pour l'exemple
    if ($ancien !== 'demo') {
        $message = "Ancien mot de passe incorrect.";
    } elseif ($nouveau !== $confirmer) {
        $message = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        // Mettre à jour le mot de passe en base de données ici
        $message = "Mot de passe changé avec succès.";
    }
}
?>
<h2>Changer mon mot de passe</h2>
<?php if ($message) echo "<p>$message</p>"; ?>
<form method="post">
    <label>Ancien mot de passe : <input type="password" name="ancien" required></label><br>
    <label>Nouveau mot de passe : <input type="password" name="nouveau" required></label><br>
    <label>Confirmer le nouveau : <input type="password" name="confirmer" required></label><br>
    <button type="submit">Changer</button>
</form>
<a href="profil.php">Retour au profil</a>
<?php require_once 'footer.php'; ?>