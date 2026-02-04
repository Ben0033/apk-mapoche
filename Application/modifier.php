<?php
require_once 'includes/bootstrap.php';

Auth::requireLogin();

// Récupérez les paramètres depuis l'URL
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

$message = '';
$message_type = '';
$entry = null;

if ($id && $type) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkCSRF();
        
        try {
            $montant = $_POST['montant'] ?? null;
            $description = sanitize($_POST['description'] ?? '');
            $categorie = sanitize($_POST['categorie'] ?? '');

            // Valider les données
            if (empty($montant) || empty($description)) {
                throw new Exception('Montant et description sont obligatoires');
            }

            if (!is_numeric($montant) || $montant <= 0) {
                throw new Exception('Montant invalide');
            }

            if ($type === 'Revenu') {
                Database::getInstance()->execute(
                    "UPDATE revenue SET montant_revenu = ?, description_revenu = ? WHERE id_revenu = ? AND id_user = ?",
                    [$montant, $description, $id, Auth::userId()]
                );
            } elseif ($type === 'Depense') {
                if (empty($categorie) || !validatePositiveInt($categorie)) {
                    throw new Exception('Catégorie invalide');
                }
                
                Database::getInstance()->execute(
                    "UPDATE depense SET montant_depense = ?, description_depense = ?, id_cat = ? WHERE id_depense = ? AND id_user = ?",
                    [$montant, $description, $categorie, $id, Auth::userId()]
                );
            } else {
                throw new Exception("Type invalide");
            }

            $message = 'Transaction modifiée avec succès!';
            $message_type = 'success';
            
            // Rediriger après succès
            header('Refresh: 2; URL=historique.php');
        } catch (Exception $e) {
            $message = $e->getMessage();
            $message_type = 'error';
        }
    } else {
        // Récupérer les données existantes
        try {
            if ($type === 'Revenu') {
                $entry = Database::getInstance()->fetch(
                    "SELECT montant_revenu AS montant, description_revenu AS description FROM revenue WHERE id_revenu = ? AND id_user = ?",
                    [$id, Auth::userId()]
                );
            } elseif ($type === 'Depense') {
                $entry = Database::getInstance()->fetch(
                    "SELECT montant_depense AS montant, description_depense AS description, id_cat AS categorie FROM depense WHERE id_depense = ? AND id_user = ?",
                    [$id, Auth::userId()]
                );
            } else {
                throw new Exception("Type invalide");
            }
        } catch (Exception $e) {
            $message = 'Erreur lors du chargement des données';
            $message_type = 'error';
        }
    }
} else {
    $message = 'Paramètres invalides';
    $message_type = 'error';
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
            <h2>Modifier</h2>
            <p class="balance-info">
                <span class="balance-label"><?= htmlspecialchars($type) ?></span>
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
        <section class="edit-section">
            <div class="edit-card">
                <h3>✏️ Modifier <?= htmlspecialchars($type) ?></h3>
                
                <?php if (!empty($message)): ?>
                    <div class="message-container">
                        <?= $message_type === 'success' ? displaySuccess($message) : displayError($message) ?>
                    </div>
                <?php endif; ?>

                <?php if ($entry): ?>
                    <form method="post" class="edit-form">
                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                        
                        <div class="form-group">
                            <label for="montant" class="form-label">💶 Montant</label>
                            <input type="number" id="montant" name="montant" class="form-input" 
                                   value="<?= htmlspecialchars($entry['montant']) ?>" 
                                   required step="0.01" min="0.01">
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">📝 Description</label>
                            <input type="text" id="description" name="description" class="form-input" 
                                   value="<?= htmlspecialchars($entry['description']) ?>" 
                                   required placeholder="Description de la transaction">
                        </div>

                        <?php if ($type === 'Depense'): ?>
                            <div class="form-group">
                                <label for="categorie" class="form-label">🏷️ Catégorie</label>
                                <select name="categorie" id="categorie" class="form-select" required>
                                    <?php
                                    try {
                                        $categories = Database::getInstance()->fetchAll(
                                            "SELECT id_cat, nom_cat FROM categorie ORDER BY nom_cat ASC"
                                        );
                                        foreach ($categories as $cat) {
                                            $selected = (isset($entry['categorie']) && $cat['id_cat'] == $entry['categorie']) ? 'selected' : '';
                                            echo "<option value=\"{$cat['id_cat']}\" $selected>" . htmlspecialchars($cat['nom_cat']) . "</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo "<option value=\"\">Erreur chargement catégories</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">✓ Mettre à jour</button>
                            <a href="historique.php" class="btn-secondary">↻ Annuler</a>
                        </div>
                    </form>
                <?php endif; ?>
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

// Focus sur le premier champ
document.getElementById('montant')?.focus();
</script>

<?php
require_once 'footer.php';
?>
