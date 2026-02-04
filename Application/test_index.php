<?php
require_once 'includes/bootstrap.php';

// Temporarily bypass login for testing
// Auth::requireLogin();

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

echo "<h1>Test Page - Categories loaded: " . count($categories) . "</h1>";
echo "<p>Database connection: " . (Database::getInstance()->ping() ? 'OK' : 'FAILED') . "</p>";
echo "<p>Bootstrap: SUCCESS</p>";

if (!empty($categories)) {
    echo "<h2>Categories found:</h2><ul>";
    foreach ($categories as $cat) {
        echo "<li>" . htmlspecialchars($cat['nom_cat']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No categories found</p>";
}

require_once 'footer.php';
?>
