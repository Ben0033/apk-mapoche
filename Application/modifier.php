<?php
require 'config.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Récupérez les paramètres depuis l'URL
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if ($id && $type) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérez les données du formulaire
        $montant = $_POST['montant'] ?? null;
        $description = $_POST['description'] ?? null;
        $categorie = $_POST['categorie'] ?? null;

        try {
            if ($type === 'Revenu') {
                // Mettre à jour la table revenue
                $stmt = $conn->prepare("UPDATE revenue SET montant_revenu = :montant, description_revenu = :description WHERE id_revenu = :id AND id_user = :id_user");
                $stmt->execute(['montant' => $montant, 'description' => $description, 'id' => $id, 'id_user' => $_SESSION['id_user']]);
            } elseif ($type === 'Depense') {
                // Mettre à jour la table depense
                $stmt = $conn->prepare("UPDATE depense SET montant_depense = :montant, description_depense = :description, id_cat = :categorie WHERE id_depense = :id AND id_user = :id_user");
                $stmt->execute(['montant' => $montant, 'description' => $description, 'categorie' => $categorie, 'id' => $id, 'id_user' => $_SESSION['id_user']]);
            } else {
                throw new Exception("Type invalide");
            }

            header('Location: historique.php');
            exit;
        } catch (Exception $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
        }
    } else {
        // Récupérez les données existantes pour pré-remplir le formulaire
        if ($type === 'Revenu') {
            $stmt = $conn->prepare("SELECT montant_revenu AS montant, description_revenu AS description FROM revenue WHERE id_revenu = :id AND id_user = :id_user");
        } elseif ($type === 'Depense') {
            $stmt = $conn->prepare("SELECT montant_depense AS montant, description_depense AS description, id_cat AS categorie FROM depense categorie WHERE id_depense = :id AND id_user = :id_user");
        } else {
            throw new Exception("Type invalide");
        }

        $stmt->execute(['id' => $id, 'id_user' => $_SESSION['id_user']]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    echo "Paramètres invalides.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    <div class="container">
        <div class="entt"><h1>Modifier <?= htmlspecialchars($type) ?></h1></div>
        <form method="POST">
            <label for="montant">Montant:</label>
            <input type="number" name="montant" id="montant" value="<?= htmlspecialchars($entry['montant']) ?>" required>

            <label for="description">Description:</label>
            <input type="text" name="description" id="description" value="<?= htmlspecialchars($entry['description']) ?>" required>

            <?php if ($type === 'Depense'): ?>
                <label for="categorie">Catégorie:</label>
                <select name="categorie" id="categorie" required>
                    <?php
                    // Récupérer les catégories depuis la base de données
                    $sql = "SELECT id_cat, nom_cat FROM categorie";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $cat) {
                        $selected = (isset($entry['categorie']) && $cat['id_cat'] == $entry['categorie']) ? 'selected' : '';
                        echo "<option value=\"{$cat['id_cat']}\" $selected>{$cat['nom_cat']}</option>";
                    }
                    ?>
                </select>
            <?php endif; ?>

            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
