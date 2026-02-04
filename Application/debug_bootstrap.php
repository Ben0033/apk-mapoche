<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing bootstrap...\n";

try {
    require_once 'includes/bootstrap.php';
    echo "✓ Bootstrap loaded successfully\n";
    
    // Test Auth class
    if (class_exists('Auth')) {
        echo "✓ Auth class exists\n";
    } else {
        echo "✗ Auth class missing\n";
    }
    
    // Test Database class
    if (class_exists('Database')) {
        echo "✓ Database class exists\n";
        $db = Database::getInstance();
        echo "✓ Database instance created\n";
        
        if ($db->ping()) {
            echo "✓ Database connection: OK\n";
        } else {
            echo "✗ Database connection: FAILED\n";
        }
    } else {
        echo "✗ Database class missing\n";
    }
    
    // Test helper functions
    if (function_exists('getCSRFToken')) {
        echo "✓ CSRF function exists\n";
    } else {
        echo "✗ CSRF function missing\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "✗ Fatal Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
