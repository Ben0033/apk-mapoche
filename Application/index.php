<?php
$title = "Accueil";
require_once 'header.php';
require_once 'config.php';

// Vérifiez si l'utilisateur est connecté
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $description = $_POST['description'] ?? null;
    $categorie = $_POST['categorie'] ?? null;
    $id_user = $_SESSION['id_user'] ?? null; // ID de l'utilisateur connecté
} else {
    $type = null;
    $montant = null;
    $description = null;
    $categorie = null;
}

// Récupérer les catégories depuis la base de données
try {
    $sql = "SELECT id_cat, nom_cat FROM categorie";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère les catégories sous forme de tableau associatif
} catch (PDOException $e) {
    $categories = [];
    $message = "Erreur lors de la récupération des catégories : " . $e->getMessage();
}
// enregistrement de la dépense ou du revenu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($type === 'dépense') {
        $sql = "INSERT INTO depense (montant_depense, date_depense,description_depense, id_cat, id_user) VALUES (:montant, now(),:description, :categorie, :id_user)";
    } elseif ($type === 'revenu') {
        $sql = "INSERT INTO revenue (montant_revenu, date_revenu,description_revenu, id_user) VALUES (:montant, now(),:description, :id_user)";
    } else {
        $message = "Type d'enregistrement invalide.";
    }
    // Vérifiez si le montant est supérieur à 0
    if ($montant <= 0) {
        $message = "Le montant doit être supérieur à 0.";
    }

    if (isset($sql)) {
        try {
            $stmt = $conn->prepare($sql);
            if ($type === 'dépense') {
                $stmt->bindParam(':categorie', $categorie);
            }
            $stmt->bindParam(':montant', $montant);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id_user', $id_user);
            if ($stmt->execute()) {
                $message = "Enregistrement réussi.";
            } else {
                $message = "Erreur lors de l'enregistrement.";
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
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
            <p id="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

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

            if (!type) {
                montantInput.style.display = 'none';
                categorieInput.style.display = 'none';
                descriptionInput.style.display = 'none';
                return;
            }

            if (type.value === 'dépense') {
                categorieInput.style.display = 'block';
                montantInput.style.display = 'block';
                descriptionInput.style.display = 'block';
            } else if (type.value === 'revenu') {
                categorieInput.style.display = 'none';
                montantInput.style.display = 'block';
                descriptionInput.style.display = 'block';
            }
        }
    </script>
</section>
<?php
require_once 'footer.php';
?>