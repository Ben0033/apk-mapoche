<?php

/**
 * Sécurité - CSRF, Sanitization, Validation
 */

/**
 * Générer et retourner token CSRF
 */
function getCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier le token CSRF
 */
function verifyCSRFToken($token = null)
{
    $token = $token ?? $_POST['csrf_token'] ?? null;

    if (!$token || !isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Vérifier CSRF et retourner erreur si invalide
 */
function checkCSRF()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verifyCSRFToken()) {
        http_response_code(403);
        die('Erreur de sécurité: token CSRF invalide');
    }
}

/**
 * Sanitizar une chaîne pour HTML
 */
function sanitize($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitizer un email
 */
function sanitizeEmail($email)
{
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

/**
 * Valider un email
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valider une chaîne non vide
 */
function validateNotEmpty($string)
{
    return !empty(trim($string));
}

/**
 * Valider un nombre > 0
 */
function validateAmount($amount)
{
    return is_numeric($amount) && floatval($amount) > 0;
}

/**
 * Valider un entier positif
 */
function validatePositiveInt($value)
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
}

/**
 * Obtenir et valider un paramètre POST sécurisé
 */
function getPost($key, $default = null)
{
    $value = $_POST[$key] ?? $default;
    return $value !== null ? sanitize($value) : null;
}

/**
 * Obtenir et valider un paramètre GET sécurisé
 */
function getQuery($key, $default = null)
{
    $value = $_GET[$key] ?? $default;
    return $value !== null ? sanitize($value) : null;
}

/**
 * Valider les extensions d'image
 */
function validateImageExtension($filename)
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, ALLOWED_IMAGE_EXTENSIONS);
}

/**
 * Générer un nom de fichier sécurisé
 */
function generateSafeFilename($originalName)
{
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return uniqid('', true) . '.' . $ext;
}

/**
 * Logguer une action sensible
 */
function logAction($action, $details = [])
{
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'action' => $action,
        'user_id' => $_SESSION['id_user'] ?? null,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
        'details' => $details
    ];

    error_log(json_encode($log_entry), 3, LOG_FILE);
}

/**
 * Rediriger de manière sécurisée
 */
function redirect($url)
{
    if (headers_sent()) {
        die('<script>window.location.href = "' . htmlspecialchars($url) . '";</script>');
    } else {
        header('Location: ' . $url);
        exit;
    }
}
