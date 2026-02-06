<?php
require_once 'includes/bootstrap.php';

$title = "Connexion";
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
<div class="auth-container">
    <div class="auth-card">
        <!-- Logo et Titre -->
        <div class="auth-header">
            <div class="auth-logo">
                <img src="images/wallet.jpg" alt="MaPoche Logo">
            </div>
            <h1 class="auth-title">MaPoche</h1>
            <p class="auth-subtitle">Gérez vos finances facilement</p>
        </div>

        <!-- Formulaire de connexion -->
        <form class="auth-form" action="" method="post">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            
            <div class="form-group">
                <label for="email_user" class="form-label">📧 Email</label>
                <input type="email" id="email_user" name="email_user" class="form-input" placeholder="votre@email.com" required value="<?= sanitize($_POST['email_user'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="mot_de_passe_user" class="form-label">🔒 Mot de passe</label>
                <input type="password" id="mot_de_passe_user" name="mot_de_passe_user" class="form-input" placeholder="••••••••" required>
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember_me">
                    <span class="checkmark"></span>
                    Se souvenir de moi
                </label>
                <a href="reset_password.php" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="message-container">
                    <?= ($message_type === 'success') ? displaySuccess($message) : displayError($message) ?>
                    
                    <?php if ($message_type === 'success'): ?>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Vider tous les champs du formulaire après connexion réussie
                                const form = document.querySelector(".auth-form");
                                if (form) {
                                    form.reset();
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-primary btn-full">🚀 Se Connecter</button>
        </form>

        <!-- Lien vers inscription -->
        <div class="auth-footer">
            <p class="auth-switch">
                Vous n'avez pas de compte ? 
                <a href="inscription.php" class="auth-link">Créer un compte</a>
            </p>
        </div>
    </div>

    <!-- Features -->
    <div class="auth-features">
        <div class="feature-item">
            <div class="feature-icon">💰</div>
            <h3>Suivi des dépenses</h3>
            <p>Enregistrez facilement vos dépenses quotidiennes</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">📊</div>
            <h3>Statistiques</h3>
            <p>Visualisez vos finances avec des graphiques</p>
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

// Focus sur le premier champ
document.getElementById('email_user')?.focus();
</script>

<?php
require_once 'footer_conn.php';
?>
