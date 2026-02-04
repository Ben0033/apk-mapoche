<?php
require_once 'includes/bootstrap.php';

$title = "Mot de passe oublié";
Auth::requireLogout(); // Rediriger si déjà connecté

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    try {
        $email = sanitizeEmail($_POST['email_user'] ?? '');

        if (empty($email)) {
            throw new Exception('Email requis');
        }

        if (!validateEmail($email)) {
            throw new Exception('Email invalide');
        }

        // Utiliser la méthode Auth pour demander la réinitialisation
        Auth::requestPasswordReset($email);
        
        $message = 'Un email de réinitialisation a été envoyé à votre adresse email.';
        $message_type = 'success';
        
        logAction('PASSWORD_RESET_REQUESTED', ['email' => $email]);
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
        logAction('PASSWORD_RESET_FAILED', ['email' => $email, 'error' => $e->getMessage()]);
    }
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
            <h1 class="auth-title">Mot de passe oublié</h1>
            <p class="auth-subtitle">Récupérez l'accès à votre compte</p>
        </div>

        <!-- Formulaire de réinitialisation -->
        <form class="auth-form" action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            
            <div class="form-group">
                <label for="email_user" class="form-label">📧 Email</label>
                <input type="email" id="email_user" name="email_user" class="form-input" 
                       placeholder="votre@email.com" required value="<?= sanitize($_POST['email_user'] ?? '') ?>">
            </div>

            <?php if (!empty($message)): ?>
                <div class="message-container">
                    <?= $message_type === 'success' ? displaySuccess($message) : displayError($message) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-primary btn-full">📧 Envoyer l'email</button>
        </form>

        <!-- Instructions -->
        <div class="auth-instructions">
            <h4>📋 Instructions</h4>
            <ul>
                <li>Entrez votre adresse email</li>
                <li>Vous recevrez un lien de réinitialisation</li>
                <li>Le lien expire après 1 heure</li>
                <li>Vérifiez vos spams si nécessaire</li>
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
            <div class="feature-icon">🔐</div>
            <h3>Sécurisé</h3>
            <p>Réinitialisation sécurisée avec token temporaire</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">⚡</div>
            <h3>Rapide</h3>
            <p>Recevez votre lien en quelques secondes</p>
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

// Focus sur le champ email
document.getElementById('email_user')?.focus();
</script>

<?php
require_once 'footer_conn.php';
?>
