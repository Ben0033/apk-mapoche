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
        <!-- Quick Stats Cards -->
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
                resetButton.addEventListener('click', function() {
                    document.getElementById('montant').style.display = 'none';
                    document.getElementById('cat').style.display = 'none';
                    document.getElementById('description').style.display = 'none';
                });
            }
        });

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

            if (type.value === 'dépense') {
                categorieInput.style.display = 'block';
                montantInput.style.display = 'block';
                descriptionInput.style.display = 'block';
                ajoutInput.style.display = 'block';
            } else if (type.value === 'revenu') {
                categorieInput.style.display = 'none';
                montantInput.style.display = 'block';
                descriptionInput.style.display = 'block';
                ajoutInput.style.display = 'none';
            }
        }
    </script>
</section>
<?php
require_once 'footer.php';
?>