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

        if (empty($email) || empty($password)) {
            throw new Exception('Email et mot de passe sont requis');
        }

        if (!validateEmail($email)) {
            throw new Exception('Email invalide');
        }

        // Tenter la connexion
        Auth::login($email, $password);
        
        $message = 'Connexion réussie! Redirection...';
        $message_type = 'success';
        
        // Rediriger vers index.php après 2 secondes
        header('Refresh: 2; URL=index.php');
    } catch (Exception $e) {
        $message = $e->getMessage();
        logAction('LOGIN_FAILED', ['email' => $email, 'error' => $e->getMessage()]);
    }
}

require_once 'header_conn.php';
?>
<form class="connexion" action="" method="post">
    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
    <h2>CONNEXION A VOTRE COMPTE</h2>

    <input type="email" name="email_user" placeholder="Email" required="required" value="<?= sanitize($_POST['email_user'] ?? '') ?>">

    <input type="password" name="mot_de_passe_user" placeholder="Mot de passe" required="required">

    <div class="mtpp">
        <div>
            <input type="checkbox" name="remember_me"> Se souvenir de moi
        </div>
        <a href="reset_password.php">Mot de passe oublié</a>
    </div>

    <?php if (!empty($message)): ?>
        <?= displayError($message) ?>
    <?php endif; ?>

    <button type="submit">Se Connecter</button>

    <p>Vous n'avez pas de compte ? <a href="inscription.php">Créer un compte</a></p>
</form>
<?php
require_once 'footer_conn.php';
?>