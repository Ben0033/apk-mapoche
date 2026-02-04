<?php

/**
 * Configuration de l'application MaPoche
 * ⚠️ En production, utiliser un fichier .env et des variables d'environnement
 */

// Configuration base de données
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'apk_mapoche');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// Configuration application
define('APP_NAME', 'MaPoche');
define('APP_DEBUG', getenv('APP_DEBUG') ?: false);
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2 MB

// Configuration sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('SESSION_SECURE', false); // true en HTTPS
define('SESSION_HTTPONLY', true);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configuration logging
define('LOG_FILE', __DIR__ . '/../logs/app.log');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Créer dossiers s'ils n'existent pas
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
if (!is_dir(dirname(LOG_FILE))) {
    mkdir(dirname(LOG_FILE), 0755, true);
}

// Configuration PHP sécurité
ini_set('display_errors', APP_DEBUG ? 1 : 0);
ini_set('log_errors', 1);
ini_set('error_log', LOG_FILE);

// Configuration sessions sécurisées
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => '/',
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTPONLY,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Initialiser CSRF token si absent
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
