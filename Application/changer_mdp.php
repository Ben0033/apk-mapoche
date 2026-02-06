<?php
require_once 'includes/bootstrap.php';

$title = "Changer mot de passe";
Auth::requireLogin();

$message = '';
$message_type = '';

// Gérer le message de succès via GET
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = 'Mot de passe changé avec succès!';
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    try {
        $ancien = $_POST['ancien'] ?? '';
        $nouveau = $_POST['nouveau'] ?? '';
        $confirmer = $_POST['confirmer'] ?? '';

        Auth::changePassword($ancien, $nouveau, $confirmer);
        
        $message = 'Mot de passe changé avec succès!';
        $message_type = 'success';
        
        // Rediriger pour éviter la double soumission
        header('Location: changer_mdp.php?success=1');
        exit;
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
    }
}

require_once 'header.php';
?>
<div class="mobile-container">
    <!-- Overlay for sidebar -->
    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
    
    <!-- Header Mobile -->
    <header class="mobile-header">
        <div class="header-top">
            <button class="menu-btn" onclick="toggleMenu()">☰</button>
            <h1 class="app-title">MaPoche</h1>
            <div class="user-avatar">
                <img src="<?= getProfilePhotoPath(Auth::user()['photo_user'] ?? '') ?>" alt="Avatar">
            </div>
        </div>
        <div class="welcome-section">
            <h2>Changer mot de passe</h2>
            <p class="balance-info">
                <span class="balance-label">Sécurisez votre compte</span>
            </p>
        </div>
    </header>

    <!-- Navigation Sidebar -->
    <nav class="side-nav" id="sideNav">
        <div class="nav-header">
            <button class="close-nav" onclick="toggleMenu()">×</button>
            <div class="nav-user">
                <img src="<?= getProfilePhotoPath(Auth::user()['photo_user'] ?? '') ?>" alt="Avatar">
                <span><?= htmlspecialchars(Auth::user()['prenom_user'] . ' ' . Auth::user()['nom_user']) ?></span>
            </div>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">🏠 Accueil</a></li>
            <li><a href="historique.php" class="nav-link">📊 Historique</a></li>
            <li><a href="profil.php" class="nav-link">👤 Profil</a></li>
            <li><a href="changer_mdp.php" class="nav-link active">🔐 Mot de passe</a></li>
            <li><a href="logout.php" class="nav-link">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <section class="password-section">
            <div class="password-card">
                <h3>🔐 Changer mon mot de passe</h3>
                
                <?php if (!empty($message)): ?>
                    <div class="message-container">
                        <?= $message_type === 'success' ? displaySuccess($message) : displayError($message) ?>
                        
                        <?php if ($message_type === 'success'): ?>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    // Vider tous les champs du formulaire après changement réussi
                                    const form = document.querySelector(".password-form");
                                    if (form) {
                                        form.reset();
                                    }
                                });
                            </script>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="password-form">
                    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="ancien" class="form-label">🔒 Ancien mot de passe</label>
                        <input type="password" id="ancien" name="ancien" class="form-input" placeholder="Entrez votre ancien mot de passe" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nouveau" class="form-label">🔑 Nouveau mot de passe</label>
                        <input type="password" id="nouveau" name="nouveau" class="form-input" placeholder="Nouveau mot de passe" required>
                        <small class="form-hint">8+ caractères, 1 majuscule, 1 chiffre</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmer" class="form-label">🔑 Confirmer le nouveau</label>
                        <input type="password" id="confirmer" name="confirmer" class="form-input" placeholder="Confirmez le nouveau mot de passe" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">✓ Changer</button>
                        <a href="profil.php" class="btn-secondary">↻ Annuler</a>
                    </div>
                </form>
            </div>
        </section>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="index.php" class="nav-item">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Accueil</span>
            </a>
            <a href="historique.php" class="nav-item">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Historique</span>
            </a>
            <a href="profil.php" class="nav-item">
                <span class="nav-icon">👤</span>
                <span class="nav-label">Profil</span>
            </a>
            <a href="logout.php" class="nav-item">
                <span class="nav-icon">🚪</span>
                <span class="nav-label">Déconnexion</span>
            </a>
        </nav>
    </main>
</div>

<script>
// Fonction pour basculer le menu
function toggleMenu() {
    const sideNav = document.getElementById('sideNav');
    const overlay = document.getElementById('overlay');
    sideNav.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Validation des mots de passe
document.getElementById('nouveau').addEventListener('input', function() {
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
document.getElementById('confirmer').addEventListener('input', function() {
    const nouveau = document.getElementById('nouveau').value;
    const confirmer = this.value;
    
    if (confirmer && nouveau !== confirmer) {
        this.style.borderColor = '#EF4444';
    } else {
        this.style.borderColor = '';
    }
});
</script>

<?php
require_once 'footer.php';
?>
