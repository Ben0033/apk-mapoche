<?php
require 'config.php';
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Récupérez les paramètres depuis l'URL
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if ($id && $type) {
    try {
        if ($type === 'Revenu') {
            // Supprimer depuis la table revenue
            $stmt = $conn->prepare("DELETE FROM revenue WHERE id_revenu = :id AND id_user = :id_user");
        } elseif ($type === 'Depense') {
            // Supprimer depuis la table depense
            $stmt = $conn->prepare("DELETE FROM depense WHERE id_depense = :id AND id_user = :id_user");
        } else {
            throw new Exception("Type invalide");
        }

        $stmt->execute(['id' => $id, 'id_user' => $_SESSION['id_user']]);
        header('Location: historique.php');
        exit;
    } catch (Exception $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Paramètres invalides.";
}
?>