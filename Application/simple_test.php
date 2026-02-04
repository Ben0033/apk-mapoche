<?php
// Test basic database connection without bootstrap
try {
    $host = 'localhost';
    $dbname = 'apk_mapoche';
    $username = 'root';
    $password = '';
    
    echo "Attempting connection to MySQL...\n";
    
    // First try to connect without database
    $conn = new PDO("mysql:host=$host", $username, $password);
    echo "MySQL connection: OK\n";
    
    // Check if database exists
    $stmt = $conn->query("SHOW DATABASES LIKE '$dbname'");
    if ($stmt->rowCount() > 0) {
        echo "Database '$dbname' exists\n";
        
        // Now connect to the database
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        echo "Database connection: OK\n";
        
        // Check if tables exist
        $tables = ['categorie', 'users', 'depense', 'revenue'];
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            echo "Table '$table': " . ($stmt->rowCount() > 0 ? "EXISTS" : "MISSING") . "\n";
        }
    } else {
        echo "Database '$dbname' does NOT exist\n";
        echo "Please run the SQL file: base_de_donner.sql\n";
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
?>
