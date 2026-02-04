<?php

/**
 * Bootstrap - Initialiser l'application
 * À inclure en première ligne de chaque fichier PHP
 */

// Inclure la configuration
require_once __DIR__ . '/config.php';

// Inclure les classes et fonctions
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

// Vérifier la connexion BD
try {
    Database::getInstance()->ping();
} catch (Exception $e) {
    die('Erreur critique: Impossible de se connecter à la base de données');
}
