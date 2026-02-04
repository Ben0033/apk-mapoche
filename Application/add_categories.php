<?php
require_once 'includes/bootstrap.php';

try {
    $db = Database::getInstance();
    
    // Add some default categories
    $categories = [
        'Alimentation',
        'Transport',
        'Logement',
        'Santé',
        'Loisirs',
        'Education',
        'Shopping',
        'Autres'
    ];
    
    foreach ($categories as $cat) {
        $db->execute("INSERT INTO categorie (nom_cat) VALUES (?)", [$cat]);
        echo "Added category: $cat\n";
    }
    
    echo "Categories added successfully!\n";
    
    // Verify
    $result = $db->fetchAll("SELECT * FROM categorie");
    echo "Total categories: " . count($result) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
