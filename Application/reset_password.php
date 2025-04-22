<?php
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email_user'], FILTER_SANITIZE_EMAIL);

    // Vérifiez si l'email existe dans la base de données
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE email_user = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Générer un jeton unique
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email_user = :email");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Construire dynamiquement le lien de réinitialisation
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/new_password.php?token=" . urlencode($token);

        // Envoyer un email avec le lien de réinitialisation
        $subject = "Réinitialisation de votre mot de passe";
        $messageBody = "Bonjour,\n\nCliquez sur ce lien pour réinitialiser votre mot de passe : $resetLink\n\nCe lien expirera dans 1 heure.";
        $headers = "From: no-reply@yourwebsite.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8";

        if (mail($email, $subject, $messageBody, $headers)) {
            $message = "Un email de réinitialisation a été envoyé à votre adresse.";
        } else {
            $message = "Une erreur est survenue lors de l'envoi de l'email. Veuillez réessayer.";
        }
    } else {
        $message = "Aucun compte trouvé avec cet email.";
    }
}
?>
<form action="" method="post">
    <h2>Réinitialiser le mot de passe</h2>
    <input type="email" name="email_user" placeholder="Email" required="required">
    <button type="submit">Envoyer</button>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</form>