<?php

/**
 * Helpers - Fonctions réutilisables
 */

/**
 * Afficher un message d'erreur formaté
 */
function displayError($message)
{
    return '<p id="message" style="background: linear-gradient(135deg, #EC4899 0%, #DB2777 100%); color: white; padding: 15px; border-radius: 12px; font-weight: 600; box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);">' . sanitize($message) . '</p>';
}

/**
 * Afficher un message de succès formaté
 */
function displaySuccess($message)
{
    return '<p id="message" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 15px; border-radius: 12px; font-weight: 600; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">' . sanitize($message) . '</p>';
}

/**
 * Obtenir le chemin relatif d'une photo avec fallback
 */
function getProfilePhotoPath($photo)
{
    if (empty($photo) || !file_exists(__DIR__ . '/../uploads/' . $photo)) {
        return 'images/default-avatar.jpg';
    }
    return 'uploads/' . $photo;
}

/**
 * Formater un montant en devise
 */
function formatAmount($amount)
{
    return number_format($amount, 2, ',', ' ') . ' €';
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y H:i')
{
    $dateTime = new DateTime($date);
    return $dateTime->format($format);
}

/**
 * Obtenir le mois en texte
 */
function getMonthName($month)
{
    $months = [
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre'
    ];
    return $months[$month - 1] ?? 'Inconnu';
}

/**
 * Paginer un résultat
 */
function paginate($items, $page = 1, $itemsPerPage = 20)
{
    $total = count($items);
    $maxPages = ceil($total / $itemsPerPage);

    // Limiter la page
    $page = max(1, min($page, $maxPages));

    $offset = ($page - 1) * $itemsPerPage;
    $pagedItems = array_slice($items, $offset, $itemsPerPage);

    return [
        'items' => $pagedItems,
        'current_page' => $page,
        'total_pages' => $maxPages,
        'total_items' => $total,
        'has_previous' => $page > 1,
        'has_next' => $page < $maxPages
    ];
}

/**
 * Obtenir les statistiques des transactions pour un utilisateur
 */
function getTransactionStats($userId)
{
    $db = Database::getInstance();

    $stats = $db->fetchOne(
        'SELECT 
            COALESCE(SUM(CASE WHEN type = "depense" THEN montant ELSE 0 END), 0) as total_depenses,
            COALESCE(SUM(CASE WHEN type = "revenu" THEN montant ELSE 0 END), 0) as total_revenus
        FROM (
            SELECT montant_depense as montant, "depense" as type FROM depense WHERE id_user = ?
            UNION ALL
            SELECT montant_revenu as montant, "revenu" as type FROM revenue WHERE id_user = ?
        ) as transactions',
        [$userId, $userId]
    );

    return $stats ? $stats : ['total_depenses' => 0, 'total_revenus' => 0];
}

/**
 * Obtenir les dépenses par catégorie
 */
function getExpensesByCategory($userId)
{
    $db = Database::getInstance();

    return $db->fetchAll(
        'SELECT categorie.nom_cat, COUNT(*) as count, SUM(depense.montant_depense) as total
        FROM depense
        JOIN categorie ON categorie.id_cat = depense.id_cat
        WHERE depense.id_user = ?
        GROUP BY depense.id_cat
        ORDER BY total DESC',
        [$userId]
    );
}

/**
 * Valider et avoir une transaction (montant, description, etc)
 */
function validateTransaction($montant, $description)
{
    $errors = [];

    if (!validateAmount($montant)) {
        $errors[] = 'Le montant doit être un nombre positif';
    }

    if (!validateNotEmpty($description)) {
        $errors[] = 'La description ne peut pas être vide';
    }

    if (strlen($description) > 255) {
        $errors[] = 'La description est trop longue (max 255 caractères)';
    }

    return $errors;
}

/**
 * Convertir un objet ou Array en JSON sécurisé
 */
function toJSON($data)
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Nettoyer un chemin de fichier
 */
function sanitizePath($path)
{
    return str_replace(['../', '..\\'], '', $path);
}

/**
 * Obtenir l'IP de l'utilisateur
 */
function getClientIP()
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ip[0]);
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED']);
        return trim($ip[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

/**
 * Vérifier le type MIME d'une image
 */
function validateImageMimeType($filePath)
{
    $mimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    return in_array($mimeType, $mimeTypes);
}
