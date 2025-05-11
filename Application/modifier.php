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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérez les données du formulaire
        $montant = $_POST['montant'] ?? null;
        $description = $_POST['description'] ?? null;
        $categorie = $_POST['categorie'] ?? null;

        try {
            if ($type === 'Revenu') {
                // Mettre à jour la table revenue
                $stmt = $conn->prepare("UPDATE revenue SET montant_revenu = :montant, description_revenu = :description WHERE id_revenu = :id AND id_user = :id_user");
                $stmt->execute(['montant' => $montant, 'description' => $description, 'id' => $id, 'id_user' => $_SESSION['id_user']]);
            } elseif ($type === 'Dépense') {
                // Mettre à jour la table depense
                $stmt = $conn->prepare("UPDATE depense SET montant_depense = :montant, description_depense = :description, id_cat = :categorie WHERE id_depense = :id AND id_user = :id_user");
                $stmt->execute(['montant' => $montant, 'description' => $description, 'categorie' => $categorie, 'id' => $id, 'id_user' => $_SESSION['id_user']]);
            } else {
                throw new Exception("Type invalide");
            }

            header('Location: historique.php');
            exit;
        } catch (Exception $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
        }
    } else {
        // Récupérez les données existantes pour pré-remplir le formulaire
        if ($type === 'Revenu') {
            $stmt = $conn->prepare("SELECT montant_revenu AS montant, description_revenu AS description FROM revenue WHERE id_revenu = :id AND id_user = :id_user");
        } elseif ($type === 'Dépense') {
            $stmt = $conn->prepare("SELECT montant_depense AS montant, description_depense AS description, id_cat AS categorie FROM depense WHERE id_depense = :id AND id_user = :id_user");
        } else {
            throw new Exception("Type invalide");
        }

        $stmt->execute(['id' => $id, 'id_user' => $_SESSION['id_user']]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} else {
    echo "Paramètres invalides.";
}
?>