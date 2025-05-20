<?php
$title = "connexion";
require_once 'header_conn.php';
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_var($_POST['email_user'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['mot_de_passe_user'];

    $stmt = $conn->prepare("SELECT id_user, email_user, mot_de_passe_user , nom_user , prenom_user, photo_user FROM users WHERE email_user = :mail");
    $stmt->bindParam(':mail', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['mot_de_passe_user'])) {
        // Set session variables
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['email_user'] = $user['email_user']; 
        $_SESSION['nom_user'] = $user['nom_user'];
        $_SESSION['prenom_user'] = $user['prenom_user'];
        $_SESSION['photo_user'] = $user['photo_user'];
    
        // Redirect to index.php
        header('Location: index.php');
        exit;
    } else {
        $message = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>
<form class="connexion" action="" method="post">
    <h2>CONNEXION A VOTRE COMPTE</h2>
    <input type="email" name="email_user" placeholder="Email" required="required" value="<?= htmlspecialchars($_POST['email_user'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <input type="password" name="mot_de_passe_user" placeholder="Mot de passe" required="required">
    
    <div class="mtpp">
        <div>
            <input type="checkbox" name="remember_me"> Se souvenir de moi
        </div>
        <a href="reset_password.php" style="text-decoration: none;">Mot de passe oublié</a>
    </div>

    <?php if (!empty($message)): ?>
        <p id="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <button type="submit">Se Connecter</button>

    <p>Vous n'avez pas de compte ? <a href="inscription.php">Créer un compte</a></p>
</form>
<?php
 require_once 'footer_conn.php'
?>