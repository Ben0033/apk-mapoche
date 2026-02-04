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
<section>
    <h1 class="h1">Bienvenue sur MaPoche</h1>
    <div class="diva">
        <p>De nos jours il est essentiel de savoir gérer ses dépenses, c'est dans ce sens que MaPoche est là pour vous aider à suivre vos dépenses au fil du temps.</p>
        <p>MaPoche vous permet d'enregistrer vos dépenses et vos revenus, mais aussi d'en garder une historique.</p>
    </div>
    <form id="formulaire" method="post" action="index.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
        <h2>Gérer vos Dépenses et Revenus</h2>
        <h4>Ajouter une Dépense ou un Revenu</h4>
        <div class="scroll-text">
            <p>Veuillez cliquer sur le type d'enregistrement que vous désirez effectuer.</p>
        </div>

        <div class="colone">
            <label>
                <input type="radio" name="type" value="dépense" onclick="afficherCategorie()" required> Dépense
            </label>
            <label>
                <input type="radio" name="type" value="revenu" onclick="afficherCategorie()" required> Revenu
            </label>
            <br><br>
        </div>

        <!-- Champs texte -->
        <input type="number" id="montant" name="montant" placeholder="Montant" style="display: none;" required>
        <br><br>

        <input type="text-area" id="description" name="description" placeholder="Description" style="display: none;" required>
        <br><br>

        <!-- Champ catégorie masqué par défaut -->
        <select name="categorie" id="cat" width="80%" style="display: none;">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['id_cat'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($cat['nom_cat'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">Aucune catégorie disponible</option>
            <?php endif; ?>
        </select>
        <br><br>

        <?php if (!empty($message)): ?>
            <?= ($message_type === 'success') ? displaySuccess($message) : displayError($message) ?>
        <?php endif; ?>
        <div class="linkAjoutCat" id="ajout">
            <a id="linkAC" href="ajoutCat.php" style="text-decoration:none;">Ajouter une catégorie</a>
        </div>
        <div class="btn">
            <button type="submit">Valider</button>
            <button class="reset" type="reset">Vider</button>
        </div>
    </form>

    <script>
        // Ajouter l'écouteur d'événement une seule fois après le chargement du DOM
        document.addEventListener('DOMContentLoaded', function() {
            const resetButton = document.querySelector('.reset');
            if (resetButton) {
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