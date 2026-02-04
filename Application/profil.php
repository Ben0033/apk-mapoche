<?php
require_once 'includes/bootstrap.php';

$title = "Profil";
Auth::requireLogin(); // Rediriger si non connecté

require_once 'header.php';

// Récupérez les informations de l'utilisateur depuis la session
$user = Auth::user();
$nom = $user['nom_user'] ?? 'Nom';
$prenom = $user['prenom_user'] ?? 'Prénom';
$photo = $user['photo_user'] ?? 'default.png';
$email = $user['email_user'] ?? '';
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
            <h2>Mon Profil</h2>
            <p class="balance-info">
                <span class="balance-label">Gérez votre compte</span>
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
            <li><a href="profil.php" class="nav-link active">👤 Profil</a></li>
            <li><a href="changer_mdp.php" class="nav-link">🔐 Mot de passe</a></li>
            <li><a href="logout.php" class="nav-link">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Profile Card -->
        <section class="profile-section">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="uploads/<?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>" alt="Photo de profil">
                    </div>
                    <div class="profile-info">
                        <h3><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="profile-email"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-label">Date d'inscription</span>
                        <span class="stat-value"><?= htmlspecialchars($_SESSION['date_inscription'] ?? 'Non disponible', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Dernière connexion</span>
                        <span class="stat-value"><?= htmlspecialchars($_SESSION['dernier_acces'] ?? 'Non disponible', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Photo Upload Section -->
        <section class="photo-section">
            <h3>📷 Changer ma photo de profil</h3>
            <div class="upload-card">
                <form action="upload_photo.php" method="post" enctype="multipart/form-data" class="upload-form">
                    <div class="upload-area">
                        <input type="file" name="photo" id="photo" accept="image/*" required>
                        <label for="photo" class="upload-label">
                            <span class="upload-icon">📷</span>
                            <span class="upload-text">Choisir une photo</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-primary">📤 Envoyer</button>
                </form>
            </div>
        </section>

        <!-- Actions Section -->
        <section class="actions-section">
            <h3>⚙️ Actions du compte</h3>
            <div class="action-cards">
                <a href="modifier_profil.php" class="action-card">
                    <div class="action-icon">✏️</div>
                    <div class="action-content">
                        <h4>Modifier mon profil</h4>
                        <p>Mettre à jour mes informations personnelles</p>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
                
                <a href="changer_mdp.php" class="action-card">
                    <div class="action-icon">🔐</div>
                    <div class="action-content">
                        <h4>Changer mon mot de passe</h4>
                        <p>Sécuriser mon accès</p>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
                
                <a href="supprimer_compte.php" class="action-card danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                    <div class="action-icon">🗑️</div>
                    <div class="action-content">
                        <h4>Supprimer mon compte</h4>
                        <p>Supprimer définitivement mon compte</p>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
                
                <a href="logout.php" class="action-card">
                    <div class="action-icon">🚪</div>
                    <div class="action-content">
                        <h4>Se déconnecter</h4>
                        <p>Quitter ma session</p>
                    </div>
                    <div class="action-arrow">→</div>
                </a>
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
            <a href="profil.php" class="nav-item active">
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

// Preview de la photo avant upload
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const label = document.querySelector('.upload-label');
    
    if (file) {
        label.querySelector('.upload-text').textContent = file.name;
        label.classList.add('has-file');
    } else {
        label.querySelector('.upload-text').textContent = 'Choisir une photo';
        label.classList.remove('has-file');
    }
});
</script>

<?php
require_once 'footer.php';
?>
