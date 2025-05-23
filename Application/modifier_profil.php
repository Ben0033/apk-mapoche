<?php


require_once 'header.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // À adapter selon ta base de données
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);

    // Met à jour la session (à remplacer par une mise à jour en base de données)
    $_SESSION['nom_user'] = $nom;
    $_SESSION['prenom_user'] = $prenom;
    $_SESSION['email_user'] = $email;

    // Redirection
    header('Location: profil.php');
    exit;
}
?>
<h2>Modifier mon profil</h2>
<form method="post">
    <label>Nom : <input type="text" name="nom" value="<?= htmlspecialchars($_SESSION['nom_user']) ?>" required></label><br>
    <label>Prénom : <input type="text" name="prenom" value="<?= htmlspecialchars($_SESSION['prenom_user']) ?>" required></label><br>
    <label>Email : <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['email_user']) ?>" required></label><br>
    <button type="submit">Enregistrer</button>
</form>
<a href="profil.php">Retour au profil</a>
<?php require_once 'footer.php'; ?>