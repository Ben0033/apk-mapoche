<?php
require_once 'includes/bootstrap.php';

$title = "Historique";
Auth::requireLogin(); // Rediriger si non connecté

require_once 'header.php';

// Récupérer les données depuis les tables revenue et depense
try {
   $historique = Database::getInstance()->fetchAll(
      "SELECT montant_revenu AS montant, description_revenu AS description, NULL AS categorie, 'Revenu' AS type, date_revenu AS date, id_revenu AS id
        FROM revenue
        WHERE id_user = ?
        UNION
        SELECT montant_depense AS montant, description_depense AS description, categorie.nom_cat AS categorie, 'Depense' AS type, date_depense AS date, id_depense AS id
        FROM depense
        INNER JOIN categorie ON categorie.id_cat = depense.id_cat
        WHERE depense.id_user = ?
        ORDER BY date DESC",
      [Auth::userId(), Auth::userId()]
   );
} catch (Exception $e) {
   $historique = [];
   error_log("Error fetching history: " . $e->getMessage());
}

// Afficher les messages de session
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
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
            <h2>Historique</h2>
            <p class="balance-info">
                <span class="balance-label">Vos transactions</span>
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
            <li><a href="historique.php" class="nav-link active">📊 Historique</a></li>
            <li><a href="profil.php" class="nav-link">👤 Profil</a></li>
            <li><a href="changer_mdp.php" class="nav-link">🔐 Mot de passe</a></li>
            <li><a href="logout.php" class="nav-link">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Stats Summary -->
        <section class="stats-cards">
            <div class="stat-card income">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <span class="stat-label">Total Revenus</span>
                    <span class="stat-value"><?= formatAmount(array_sum(array_column(array_filter($historique, fn($item) => $item['type'] === 'Revenu'), 'montant'))) ?></span>
                </div>
            </div>
            <div class="stat-card expense">
                <div class="stat-icon">💸</div>
                <div class="stat-info">
                    <span class="stat-label">Total Dépenses</span>
                    <span class="stat-value"><?= formatAmount(array_sum(array_column(array_filter($historique, fn($item) => $item['type'] === 'Depense'), 'montant'))) ?></span>
                </div>
            </div>
        </section>

        <!-- Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="message-container">
                <?= displaySuccess($success_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="message-container">
                <?= displayError($error_message) ?>
            </div>
        <?php endif; ?>

        <!-- History List -->
        <section class="history-section">
            <h3>Historique des Transactions</h3>
            
            <?php if (!empty($historique)): ?>
                <div class="transaction-list">
                    <?php foreach ($historique as $entry): ?>
                        <div class="transaction-item <?= strtolower($entry['type']) ?>">
                            <div class="transaction-icon">
                                <?= $entry['type'] === 'Revenu' ? '💰' : '💸' ?>
                            </div>
                            <div class="transaction-details">
                                <div class="transaction-amount <?= $entry['type'] === 'Revenu' ? 'income' : 'expense' ?>">
                                    <?= $entry['type'] === 'Revenu' ? '+' : '-' ?><?= formatAmount($entry['montant']) ?>
                                </div>
                                <div class="transaction-description">
                                    <?= htmlspecialchars($entry['description']) ?>
                                </div>
                                <?php if ($entry['type'] === 'Depense' && $entry['categorie']): ?>
                                    <div class="transaction-category">
                                        🏷️ <?= htmlspecialchars($entry['categorie']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="transaction-date">
                                    📅 <?= date('d/m/Y', strtotime($entry['date'])) ?>
                                </div>
                            </div>
                            <div class="transaction-actions">
                                <a href="modifier.php?id=<?= $entry['id'] ?>&type=<?= $entry['type'] ?>" class="action-btn edit">✏️</a>
                                <a href="supprimer.php?id=<?= $entry['id'] ?>&type=<?= $entry['type'] ?>" class="action-btn delete" onclick="return confirm('Supprimer cette transaction ?')">🗑️</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3>Aucune transaction</h3>
                    <p>Commencez par ajouter votre première transaction</p>
                    <a href="index.php" class="btn-primary">Ajouter une transaction</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="index.php" class="nav-item">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Accueil</span>
            </a>
            <a href="historique.php" class="nav-item active">
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
