<?php
require_once 'includes/bootstrap.php';

$title = "Ajouter catégorie";
Auth::requireLogin(); // Rediriger si non connecté

$message = '';
$message_type = '';

// Gérer le message de succès via GET
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $message = "Catégorie ajoutée avec succès!";
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF();
    
    try {
        $categorie = sanitize(trim($_POST['categorie'] ?? ''));

        if (empty($categorie)) {
            throw new Exception("Le champ catégorie ne peut pas être vide.");
        }

        if (strlen($categorie) > 50) {
            throw new Exception("Le nom de la catégorie ne peut pas dépasser 50 caractères.");
        }

        // Vérifier si la catégorie existe déjà
        $existing = Database::getInstance()->fetch(
            "SELECT id_cat FROM categorie WHERE nom_cat = ?",
            [$categorie]
        );
        
        if ($existing) {
            throw new Exception("Cette catégorie existe déjà.");
        }

        Database::getInstance()->execute(
            "INSERT INTO categorie (nom_cat) VALUES (?)",
            [$categorie]
        );

        $message = "Catégorie ajoutée avec succès!";
        $message_type = 'success';
        
        logAction('CATEGORY_ADDED', ['categorie' => $categorie]);
        
        // Rediriger pour éviter la double soumission
        header('Location: ajoutCat.php?success=1');
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
            <h2>Ajouter une catégorie</h2>
            <p class="balance-info">
                <span class="balance-label">Organisez vos dépenses</span>
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
        <section class="category-section">
            <div class="category-card">
                <h3>🏷️ Ajouter une catégorie</h3>
                
                <?php if (!empty($message)): ?>
                    <div class="message-container">
                        <?= $message_type === 'success' ? displaySuccess($message) : displayError($message) ?>
                        
                        <?php if ($message_type === 'success'): ?>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    // Vider le champ catégorie après ajout réussi
                                    const form = document.querySelector(".category-form");
                                    const input = document.getElementById("categorie");
                                    if (form && input) {
                                        form.reset();
                                        input.focus();
                                    }
                                });
                            </script>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="category-form">
                    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                    
                    <div class="form-group">
                        <label for="categorie" class="form-label">📝 Nom de la catégorie</label>
                        <input type="text" id="categorie" name="categorie" class="form-input" 
                               placeholder="Ex: Restaurant, Transport, Shopping..." 
                               required maxlength="50"
                               value="<?= htmlspecialchars($_POST['categorie'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <small class="form-hint">Maximum 50 caractères</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">✓ Ajouter</button>
                        <a href="index.php" class="btn-secondary">↻ Retour</a>
                    </div>
                </form>

                <!-- Catégories existantes -->
                <div class="existing-categories">
                    <h4>📋 Catégories existantes</h4>
                    <?php
                    try {
                        $categories = Database::getInstance()->fetchAll(
                            "SELECT nom_cat FROM categorie ORDER BY nom_cat ASC"
                        );
                        
                        if (!empty($categories)):
                    ?>
                        <div class="category-list">
                            <?php foreach ($categories as $cat): ?>
                                <span class="category-tag"><?= htmlspecialchars($cat['nom_cat']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php 
                        else:
                    ?>
                        <p class="no-categories">Aucune catégorie existante</p>
                    <?php 
                        endif;
                    } catch (Exception $e) {
                        echo '<p class="error">Erreur lors du chargement des catégories</p>';
                    }
                    ?>
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

// Focus sur le champ catégorie
document.getElementById('categorie')?.focus();
</script>

<?php
require_once 'footer.php';
?>
