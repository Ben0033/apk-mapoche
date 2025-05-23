<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Connexion à la base de données (à adapter selon ta config)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=apk_mapoche;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $dossier = 'uploads/';
    $fichier = basename($_FILES['photo']['name']);
    $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
    $autorisees = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extension, $autorisees)) {
        $nouveau_nom = uniqid() . '.' . $extension;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dossier . $nouveau_nom)) {
            // Met à jour la base de données
            $stmt = $pdo->prepare('UPDATE users SET photo_user = ? WHERE id_user = ?');
            $stmt->execute([$nouveau_nom, $_SESSION['id_user']]);
            // Met à jour la session
            $_SESSION['photo_user'] = $nouveau_nom;
        }
    }
}
header('Location: profil.php');
exit;