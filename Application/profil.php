<?php
require_once 'includes/bootstrap.php';

$title = "Profil";
Auth::requireLogin(); // Rediriger si non connecté

require_once 'header.php';

// Récupérez les informations de l'utilisateur depuis la session
$user = Auth::user();
$nom = $user['nom_user'] ?? 'Nom';
$prenom = $user['prenom_user'] ?? 'Prénom';
$photo = $user['photo_user'] ?? 'default.png';
$email = $user['email_user'] ?? '';
?>
<div class="img_profil">
    <img src="uploads/<?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>" alt="image de profil" class="photo-profil">
    <h2><?= htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email_user'], ENT_QUOTES, 'UTF-8') ?></p>
    <div style="display: flex;justify-content: space-between;width: 50%;">
        <p><strong>Date d'inscription:</strong> <?= htmlspecialchars($_SESSION['date_inscription'] ?? 'Non disponible', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Dernière connexion:</strong> <?= htmlspecialchars($_SESSION['dernier_acces'] ?? 'Non disponible', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div style="width: 100%;Text-align: center;">
        <h3>Changer ma photo de profil</h3>
        <form action="upload_photo.php" method="post" enctype="multipart/form-data">
            <label for="photo"></label>
            <input type="file" name="photo" id="photo" accept="image/*" required>
            <button type="submit">Envoyer</button>
        </form>
    </div>

    <!-- <h3>Mes activités récentes</h3>
    <ul>
        <li>Connexion le <?= htmlspecialchars($_SESSION['dernier_acces'] ?? 'Non disponible', ENT_QUOTES, 'UTF-8') ?></li>
        <!-- Ajoute ici des activités dynamiques si tu en as -->
    </ul>
</div>
<div class="img_profil">
    <div class="pro">
        <a href="modifier_profil.php" class="btns">Modifier mon profil</a>
        <a href="changer_mdp.php" class="btns">Changer mon mot de passe</a>
        <a href="supprimer_compte.php" class="btns" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">Supprimer mon compte</a>
        <a href="logout.php" class="btns">Se déconnecter</a>
    </div>
</div>
<?php
require_once 'footer.php';
?>