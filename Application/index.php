<?php
require_once 'includes/bootstrap.php';

$title = "Accueil";
Auth::requireLogin(); // Rediriger si non connecté

require_once 'header.php';

$message = '';
$message_type = '';
$type = null;
$montant = null;
$description = null;
$categorie = null;

// Récupérer les catégories
try {
    $categories = Database::getInstance()->fetchAll(
        "SELECT id_cat, nom_cat FROM categorie ORDER BY nom_cat ASC"
    );
} catch (Exception $e) {
    $categories = [];
    $message = 'Erreur lors du chargement des catégories';
    $message_type = 'error';
}

// Récupérer les statistiques des dépenses par catégorie
try {
    $expenses_by_category = getExpensesByCategory(Auth::userId());
    $stats = getTransactionStats(Auth::userId());
} catch (Exception $e) {
    $expenses_by_category = [];
    $stats = ['total_depenses' => 0, 'total_revenus' => 0];
}

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF(); // Vérifier CSRF

    try {
        $type = sanitize($_POST['type'] ?? '');
        $montant = $_POST['montant'] ?? '';
        $description = sanitize($_POST['description'] ?? '');
        $categorie = sanitize($_POST['categorie'] ?? '');
        $id_user = Auth::userId();

        // Valider le type
        if (!in_array($type, ['dépense', 'revenu'])) {
            throw new Exception('Type d\'enregistrement invalide');
        }

        // Valider montant et description
        $validation_errors = validateTransaction($montant, $description);
        if (!empty($validation_errors)) {
            throw new Exception(implode(', ', $validation_errors));
        }

        // Insérer la transaction
        if ($type === 'dépense') {
            if (empty($categorie) || !validatePositiveInt($categorie)) {
                throw new Exception('Catégorie invalide');
            }

            Database::getInstance()->execute(
                'INSERT INTO depense (montant_depense, date_depense, description_depense, id_cat, id_user) 
                 VALUES (?, NOW(), ?, ?, ?)',
                [$montant, $description, $categorie, $id_user]
            );

            $message = '✓ Dépense enregistrée avec succès';
            $message_type = 'success';
            logAction('EXPENSE_ADDED', ['montant' => $montant, 'categorie' => $categorie]);
        } else { // revenu
            Database::getInstance()->execute(
                'INSERT INTO revenue (montant_revenu, date_revenu, description_revenu, id_user) 
                 VALUES (?, NOW(), ?, ?)',
                [$montant, $description, $id_user]
            );

            $message = '✓ Revenu enregistré avec succès';
            $message_type = 'success';
            logAction('REVENUE_ADDED', ['montant' => $montant]);
        }

        // Reset les champs après succès
        $type = null;
        $montant = null;
        $description = null;
        $categorie = null;
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
        logAction('TRANSACTION_FAILED', ['error' => $e->getMessage()]);
    }
}

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
            <h2>Bonjour, <?= htmlspecialchars(Auth::user()['prenom_user'] ?? '') ?></h2>
            <p class="balance-info">
                <span class="balance-label">Solde Actuel</span><br>
                <span class="balance-amount"><?= formatAmount($stats['total_revenus'] - $stats['total_depenses']) ?></span>
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
            <li><a href="index.php" class="nav-link active">🏠 Accueil</a></li>
            <li><a href="historique.php" class="nav-link">📊 Historique</a></li>
            <li><a href="profil.php" class="nav-link">👤 Profil</a></li>
            <li><a href="changer_mdp.php" class="nav-link">🔐 Mot de passe</a></li>
            <li><a href="logout.php" class="nav-link">🚪 Déconnexion</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <section class="stats-cards">
            <div class="stat-card income">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <span class="stat-label">Revenus</span>
                    <span class="stat-value"><?= formatAmount($stats['total_revenus']) ?></span>
                </div>
            </div>
            <div class="stat-card expense">
                <div class="stat-icon">💸</div>
                <div class="stat-info">
                    <span class="stat-label">Dépenses</span>
                    <span class="stat-value"><?= formatAmount($stats['total_depenses']) ?></span>
                </div>
            </div>
        </section>

        <!-- Chart Section -->
        <section class="chart-section">
            <h3>Dépenses par Catégorie</h3>
            <div class="chart-container">
                <canvas id="expenseChart" width="300" height="300"></canvas>
                <div id="chartLegend" class="chart-legend"></div>
            </div>
        </section>

        <!-- Transaction Form -->
        <section class="transaction-form">
            <form id="formulaire" method="post" action="index.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
                <h3>Ajouter une Transaction</h3>
                <div class="form-intro">
                    <p>Enregistrez rapidement vos dépenses et revenus</p>
                </div>

                <div class="transaction-type">
                    <label class="type-option">
                        <input type="radio" name="type" value="dépense" onclick="afficherCategorie()" required>
                        <span class="type-label expense-type">💸 Dépense</span>
                    </label>
                    <label class="type-option">
                        <input type="radio" name="type" value="revenu" onclick="afficherCategorie()" required>
                        <span class="type-label income-type">💰 Revenu</span>
                    </label>
                </div>

                <div class="form-fields">
                    <input type="number" id="montant" name="montant" class="form-input" placeholder="💶 Montant" style="display: none;" required step="0.01">

                    <input type="text" id="description" name="description" class="form-input" placeholder="📝 Description" style="display: none;" required>

                    <select name="categorie" id="cat" class="form-select" style="display: none;">
                        <?php if (!empty($categories)): ?>
                            <option value="">🏷️ Choisir une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id_cat'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($cat['nom_cat'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Aucune catégorie disponible</option>
                        <?php endif; ?>
                    </select>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="message-container">
                        <?= ($message_type === 'success') ? displaySuccess($message) : displayError($message) ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">✓ Valider</button>
                    <button type="reset" class="btn-secondary">↻ Vider</button>
                </div>
                
                <div class="add-category-link" id="ajout" style="display: none;">
                    <a href="ajoutCat.php" class="link-category">+ Ajouter une catégorie</a>
                </div>
            </form>
        </section>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="index.php" class="nav-item active">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Accueil</span>
            </a>
            <a href="historique.php" class="nav-item">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Stats</span>
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

<?php
require_once 'footer.php';
?>

<script>
// Données pour le graphique
const expenseData = <?= json_encode($expenses_by_category) ?>;

// Fonction pour basculer le menu
function toggleMenu() {
    const sideNav = document.getElementById('sideNav');
    const overlay = document.getElementById('overlay');
    sideNav.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Fonction pour afficher/masquer les champs
function afficherCategorie() {
    const type = document.querySelector('input[name="type"]:checked');
    const categorieInput = document.getElementById('cat');
    const montantInput = document.getElementById('montant');
    const descriptionInput = document.getElementById('description');
    const ajoutInput = document.getElementById('ajout');

    if (!type) {
        montantInput.style.display = 'none';
        categorieInput.style.display = 'none';
        descriptionInput.style.display = 'none';
        ajoutInput.style.display = 'none';
        return;
    }

    // Toujours afficher Montant et Description quand un type est sélectionné
    montantInput.style.display = 'block';
    descriptionInput.style.display = 'block';

    if (type.value === 'dépense') {
        categorieInput.style.display = 'block';
        ajoutInput.style.display = 'block';
    } else if (type.value === 'revenu') {
        categorieInput.style.display = 'none';
        ajoutInput.style.display = 'none';
    }
}

// Reset button functionality
document.addEventListener('DOMContentLoaded', function() {
    const resetButton = document.querySelector('.btn-secondary');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            document.getElementById('montant').style.display = 'none';
            document.getElementById('cat').style.display = 'none';
            document.getElementById('description').style.display = 'none';
            document.getElementById('ajout').style.display = 'none';
        });
    }

    // Dessiner le graphique circulaire
    drawPieChart();
});

// Fonction pour dessiner le graphique circulaire
function drawPieChart() {
    const canvas = document.getElementById('expenseChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(centerX, centerY) - 20;
    
    // Couleurs pour le graphique
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
        '#9966FF', '#FF9F40', '#C9CBCF', '#FF99CC'
    ];
    
    // Calculer le total
    const total = expenseData.reduce((sum, item) => sum + parseFloat(item.total), 0);
    
    if (total === 0) {
        ctx.fillStyle = '#666';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Aucune dépense enregistrée', centerX, centerY);
        return;
    }
    
    let currentAngle = -Math.PI / 2;
    
    // Dessiner les segments
    expenseData.forEach((item, index) => {
        const value = parseFloat(item.total);
        const sliceAngle = (value / total) * 2 * Math.PI;
        
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
        ctx.lineTo(centerX, centerY);
        ctx.fillStyle = colors[index % colors.length];
        ctx.fill();
        
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = 2;
        ctx.stroke();
        
        currentAngle += sliceAngle;
    });
    
    // Créer la légende
    const legendContainer = document.getElementById('chartLegend');
    if (legendContainer) {
        legendContainer.innerHTML = '';
        expenseData.forEach((item, index) => {
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';
            legendItem.innerHTML = `
                <span class="legend-color" style="background-color: ${colors[index % colors.length]}"></span>
                <span class="legend-label">${item.nom_cat}</span>
                <span class="legend-value">${formatAmount(parseFloat(item.total))}</span>
            `;
            legendContainer.appendChild(legendItem);
        });
    }
}

// Fonction pour formater les montants
function formatAmount(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}
</script>
