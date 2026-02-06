<?php
require_once 'includes/bootstrap.php';

$title = "Test Devise";
Auth::requireLogin();

// Tests de formatage avec différents montants
$test_amounts = [0, 100, 1000, 10000, 100000, 1000000];

require_once 'header.php';
?>
<div class="mobile-container">
    <!-- Overlay for sidebar -->
    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
    
    <!-- Header Mobile -->
    <header class="mobile-header">
        <div class="header-top">
            <button class="menu-btn" onclick="toggleMenu()">☰</button>
            <h1 class="app-title">Test Devise</h1>
            <div class="user-avatar">
                <img src="<?= getProfilePhotoPath(Auth::user()['photo_user'] ?? '') ?>" alt="Avatar">
            </div>
        </div>
        <div class="welcome-section">
            <h2>Configuration de la devise</h2>
            <p class="balance-info">
                <span class="balance-label">Devise actuelle</span>
                <span class="balance-amount"><?= getCurrencyInfo()['name'] ?></span>
            </p>
            <p class="currency-info">
                <small>Code: <?= getCurrencyInfo()['code'] ?> | Symbole: <?= getCurrencyInfo()['symbol'] ?> | Décimales: <?= getCurrencyInfo()['decimals'] ?></small>
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
            <li><a href="changer_mdp.php" class="nav-link">🔐 Mot de passe</a></li>
            <li><a href="logout.php" class="nav-link">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <section class="test-section">
            <div class="test-card">
                <h3>🧪 Tests de formatage monétaire</h3>
                
                <div class="test-grid">
                    <?php foreach ($test_amounts as $amount): ?>
                        <div class="test-item">
                            <div class="test-raw">
                                <small>Brut:</small>
                                <span><?= number_format($amount, 0, '.', ',') ?></span>
                            </div>
                            <div class="test-formatted">
                                <small>Formaté:</small>
                                <span class="amount-display"><?= formatAmount($amount) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="test-info">
                    <h4>📋 Informations de configuration</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Code devise:</label>
                            <span><?= getCurrencyInfo()['code'] ?></span>
                        </div>
                        <div class="info-item">
                            <label>Symbole:</label>
                            <span><?= getCurrencyInfo()['symbol'] ?></span>
                        </div>
                        <div class="info-item">
                            <label>Nom:</label>
                            <span><?= getCurrencyInfo()['name'] ?></span>
                        </div>
                        <div class="info-item">
                            <label>Décimales:</label>
                            <span><?= getCurrencyInfo()['decimals'] ?></span>
                        </div>
                    </div>
                </div>

                <div class="test-actions">
                    <a href="index.php" class="btn-primary">🏠 Retour à l'accueil</a>
                </div>
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
</script>

<?php
require_once 'footer.php';
?>
