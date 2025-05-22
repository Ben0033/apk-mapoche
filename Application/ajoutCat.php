<?php
$title = "connexion";
require_once 'header_conn.php';
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categorie = trim($_POST['categorie']);

    if (!empty($categorie)) {
        $stmt = $conn->prepare("INSERT INTO categorie (nom_cat) VALUES (:categorie)");
        $stmt->bindParam(':categorie', $categorie, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $message = "Catégorie ajoutée avec succès.";
        } else {
            $message = "Erreur lors de l'ajout de la catégorie.";
        }
    } else {
        $message = "Le champ catégorie ne peut pas être vide.";
    }
}
?>
<form class="connexion" action="" method="post">
    <h2>Ajouter une catégorie</h2>
    <input type="text" name="categorie" placeholder="Ajouter vos catégorie" required="required" value="<?= htmlspecialchars($_POST['categorie'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <?php if (!empty($message)): ?>
        <p id="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <button type="submit">Ajouter</button>

    <p><a href="index.php">Faire enregistrement</a></p>
</form>
<?php
 require_once 'footer_conn.php'
?>