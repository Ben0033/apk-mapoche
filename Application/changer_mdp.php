<?php

require_once 'includes/bootstrap.php';

Auth::requireLogin();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    try {
        $ancien = $_POST['ancien'] ?? '';
        $nouveau = $_POST['nouveau'] ?? '';
        $confirmer = $_POST['confirmer'] ?? '';

        Auth::changePassword($ancien, $nouveau, $confirmer);
        
        $message = 'Mot de passe changé avec succès!';
        $message_type = 'success';
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
    }
}

require_once 'header.php';
?>
<h2>Changer mon mot de passe</h2>

<?php if (!empty($message)): ?>
    <?= $message_type === 'success' ? displaySuccess($message) : displayError($message) ?>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
    <label>Ancien mot de passe : <input type="password" name="ancien" required></label><br>
    <label>Nouveau mot de passe : <input type="password" name="nouveau" required></label><br>
    <label>Confirmer le nouveau : <input type="password" name="confirmer" required></label><br>
    <button type="submit">Changer</button>
</form>
<a href="profil.php">Retour au profil</a>
<?php require_once 'footer.php'; ?>