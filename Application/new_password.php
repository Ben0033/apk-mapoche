<?php
$title = "Réinitialiser le mot de passe";
require_once 'header.php';
session_start();
// Vérifiez si l'utilisateur est déjà connecté
if (isset($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}
// Inclure le fichier de configuration pour la connexion à la base de données
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Vérifiez si les mots de passe correspondent
    if ($newPassword === $confirmPassword) {
        // Vérifiez si le mot de passe respecte les critères de sécurité
        if (strlen($newPassword) < 8) {
            $message = "Le mot de passe doit contenir au moins 8 caractères.";
        } elseif (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $message = "Le mot de passe doit contenir au moins une lettre majuscule et un chiffre.";
        } else {
            // Vérifiez si le jeton est valide
            $stmt = $conn->prepare("SELECT id_user FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Mettre à jour le mot de passe
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET mot_de_passe_user = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id_user = :id");
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':id', $user['id_user']);
                $stmt->execute();

                $message = "Votre mot de passe a été réinitialisé avec succès. Vous serez redirigé vers la page de connexion.";
                // Redirigez l'utilisateur vers la page de connexion après 3 secondes
                header("refresh:3;url=connexion.php");
                exit;
            } else {
                $message = "Le lien de réinitialisation est invalide ou a expiré.";
            }
        }
    } else {
        $message = "Les mots de passe ne correspondent pas.";
    }
} elseif (isset($_GET['token'])) {
    // Validez le jeton reçu via GET
    $token = filter_var($_GET['token'], FILTER_SANITIZE_STRING);
} else {
    header('Location: connexion.php');
    exit;
}
?>
<form action="" method="post">
    <h2>Définir un nouveau mot de passe</h2>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
    <input type="password" name="new_password" placeholder="Nouveau mot de passe" required="required">
    <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required="required">
    <button type="submit">Réinitialiser</button>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</form>