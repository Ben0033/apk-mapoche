<?php
try {
    require_once 'includes/bootstrap.php';
    echo "Bootstrap loaded successfully\n";
    
    $db = Database::getInstance();
    echo "Database instance created\n";
    
    if ($db->ping()) {
        echo "Database connection: OK\n";
        
        // Test categories query
        $categories = $db->fetchAll("SELECT id_cat, nom_cat FROM categorie ORDER BY nom_cat ASC");
        echo "Categories query executed, found " . count($categories) . " categories\n";
        
    } else {
        echo "Database connection: FAILED\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
