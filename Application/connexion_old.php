<?php
require_once 'includes/bootstrap.php';

$title = "connexion";
Auth::requireLogout(); // Rediriger si déjà connecté

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF(); // Vérifier le token CSRF

    try {
        $email = sanitizeEmail($_POST['email_user'] ?? '');
        $password = $_POST['mot_de_passe_user'] ?? '';

        if (!$email || !$password) {
            throw new Exception('Email et mot de passe requis');
        }

        Auth::login($email, $password);
        redirect('index.php');
        exit;
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

require_once 'header_conn.php';
?>
<form class="connexion" action="" method="post">
    <h2>CONNEXION A VOTRE COMPTE</h2>
    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
    <input type="email" name="email_user" placeholder="Email" required="required" value="<?= sanitize($_POST['email_user'] ?? '') ?>">
    <input type="password" name="mot_de_passe_user" placeholder="Mot de passe" required="required">

    <div class="mtpp">
        <div>
            <input type="checkbox" name="remember_me"> Se souvenir de moi
        </div>
        <a href="reset_password.php" style="text-decoration: none;">Mot de passe oublié</a>
    </div>

    <?php if (!empty($message)): ?>
        <?= displayError($message) ?>
    <?php endif; ?>

    <button type="submit">Se Connecter</button>

    <p>Vous n'avez pas de compte ? <a href="inscription.php">Créer un compte</a></p>
</form>
<?php
require_once 'footer_conn.php'
?>