<?php
require_once 'includes/bootstrap.php';

$title = "Nouveau mot de passe";
Auth::requireLogout(); // Rediriger si déjà connecté

$message = '';
$message_type = '';
$token = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    try {
        $token = sanitize($_POST['token'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
            throw new Exception('Tous les champs sont obligatoires');
        }

        // Utiliser la méthode Auth pour réinitialiser le mot de passe
        Auth::resetPassword($token, $newPassword, $confirmPassword);
        
        $message = 'Votre mot de passe a été réinitialisé avec succès! Redirection vers la connexion...';
        $message_type = 'success';
        
        logAction('PASSWORD_RESET_SUCCESS', ['token' => substr($token, 0, 10) . '...']);
        
        // Rediriger vers la connexion après 3 secondes
        header('Refresh: 3; URL=connexion.php');
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
        logAction('PASSWORD_RESET_FAILED', ['error' => $e->getMessage()]);
    }
} elseif (isset($_GET['token'])) {
    $token = sanitize($_GET['token']);
} else {
    // Rediriger vers la page de demande de réinitialisation si pas de token
    header('Location: reset_password.php');
    exit;
}

require_once 'header_conn.php';
?>
<div class="auth-container">
    <div class="auth-card">
        <!-- Logo et Titre -->
        <div class="auth-header">
            <div class="auth-logo">
                <img src="images/wallet.jpg" alt="MaPoche Logo">
            </div>
            <h1 class="auth-title">Nouveau mot de passe</h1>
            <p class="auth-subtitle">Définissez votre nouveau mot de passe</p>
        </div>

        <!-- Formulaire de nouveau mot de passe -->
        <form class="auth-form" action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="new_password" class="form-label">🔑 Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" class="form-input" 
                       placeholder="Entrez votre nouveau mot de passe" required>
                <small class="form-hint">8+ caractères, 1 majuscule, 1 chiffre</small>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">🔑 Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                       placeholder="Confirmez votre nouveau mot de passe" required>
            </div>

            <?php if (!empty($message)): ?>
                <div class="message-container">
                    <?= $message_type === 'success' ? displaySuccess($message) : displayError($message) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-primary btn-full">🔐 Réinitialiser le mot de passe</button>
        </form>

        <!-- Instructions -->
        <div class="auth-instructions">
            <h4>📋 Exigences de sécurité</h4>
            <ul>
                <li>Minimum 8 caractères</li>
                <li>Au moins une lettre majuscule</li>
                <li>Au moins un chiffre</li>
                <li>Évitez les mots de passe courants</li>
            </ul>
        </div>

        <!-- Lien vers connexion -->
        <div class="auth-footer">
            <p class="auth-switch">
                Vous vous souvenez de votre mot de passe ? 
                <a href="connexion.php" class="auth-link">Se Connecter</a>
            </p>
        </div>
    </div>

    <!-- Features -->
    <div class="auth-features">
        <div class="feature-item">
            <div class="feature-icon">🛡️</div>
            <h3>Sécurisé</h3>
            <p>Mot de passe hashé avec bcrypt</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">⏰</div>
            <h3>Temporaire</h3>
            <p>Le lien expire après 1 heure</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">📱</div>
            <h3>Mobile</h3>
            <p>Interface adaptée pour tous vos appareils</p>
        </div>
    </div>
</div>

<script>
// Animation du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const authCard = document.querySelector('.auth-card');
    const authFeatures = document.querySelectorAll('.feature-item');
    
    // Animation d'entrée
    setTimeout(() => {
        authCard.style.opacity = '1';
        authCard.style.transform = 'translateY(0)';
    }, 100);
    
    // Animation des features
    authFeatures.forEach((feature, index) => {
        setTimeout(() => {
            feature.style.opacity = '1';
            feature.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
});

// Validation des mots de passe
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const hints = document.querySelectorAll('.form-hint');
    
    let valid = true;
    if (password.length < 8) valid = false;
    if (!/[A-Z]/.test(password)) valid = false;
    if (!/[0-9]/.test(password)) valid = false;
    
    hints.forEach(hint => {
        hint.style.color = valid ? '#10B981' : '#EF4444';
    });
});

// Vérification de la correspondance
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        this.style.borderColor = '#EF4444';
    } else {
        this.style.borderColor = '';
    }
});

// Focus sur le premier champ
document.getElementById('new_password')?.focus();
</script>

<?php
require_once 'footer_conn.php';
?>
