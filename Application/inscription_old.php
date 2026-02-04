<?php
require_once 'includes/bootstrap.php';

$title = "Inscription";
Auth::requireLogout(); // Rediriger si déjà connecté

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRF(); // Vérifier le token CSRF

    try {
        // Récupérer et valider les inputs
        $email = sanitizeEmail($_POST['email_user'] ?? '');
        $password = $_POST['mot_de_passe_user'] ?? '';
        $confirm_password = $_POST['confirmer_mot_de_passe_user'] ?? '';
        $nom = sanitize($_POST['nom_user'] ?? '');
        $prenom = sanitize($_POST['prenom_user'] ?? '');

        // Valider les champs
        if (empty($email) || empty($password) || empty($nom) || empty($prenom)) {
            throw new Exception('Tous les champs sont obligatoires');
        }

        if (!validateEmail($email)) {
            throw new Exception('Email invalide');
        }

        if ($password !== $confirm_password) {
            throw new Exception('Les mots de passe ne correspondent pas');
        }

        // Gérer l'upload de photo
        $photo_path = null;
        if (isset($_FILES['photo_user']) && $_FILES['photo_user']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo_user'];

            // Vérifier la taille
            if ($file['size'] > MAX_UPLOAD_SIZE) {
                throw new Exception('Le fichier est trop volumineux (max ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . ' MB)');
            }

            // Vérifier l'extension
            if (!validateImageExtension($file['name'])) {
                throw new Exception('Format d\'image non accepté');
            }

            // Vérifier le type MIME
            if (!validateImageMimeType($file['tmp_name'])) {
                throw new Exception('Le fichier n\'est pas une image valide');
            }

            // Générer un nom sécurisé
            $photo_path = generateSafeFilename($file['name']);
            $upload_path = UPLOAD_DIR . $photo_path;

            // Créer le dossier s'il n'existe pas
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }

            // Déplacer le fichier
            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                throw new Exception('Erreur lors du téléversement de la photo');
            }
        }

        // Enregistrer l'utilisateur
        Auth::register($email, $password, $nom, $prenom, $photo_path);

        $message = 'Inscription réussie! Redirection vers la connexion...';
        $message_type = 'success';

        // Rediriger vers la connexion après 2 secondes
        header('Refresh: 2; URL=connexion.php');
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
        logAction('REGISTRATION_FAILED', ['error' => $e->getMessage()]);
    }
}

require_once 'header_conn.php';
?>
<form class="connexion" action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
    <input type="text" placeholder="NOM" required="required" name="nom_user" value="<?= sanitize($_POST['nom_user'] ?? '') ?>">
    <input type="text" placeholder="PRENOM" required="required" name="prenom_user" value="<?= sanitize($_POST['prenom_user'] ?? '') ?>">
    <input type="email" placeholder="Email" required="required" name="email_user" value="<?= sanitize($_POST['email_user'] ?? '') ?>">
    <input type="password" placeholder="Mot de passe" required="required" name="mot_de_passe_user">
    <input type="password" placeholder="Confirmer le mot de passe" required="required" name="confirmer_mot_de_passe_user">
    <input type="file" name="photo_user" accept="image/*" required="required">

    <?php if (!empty($message)): ?>
        <?php echo ($message_type === 'success') ? displaySuccess($message) : displayError($message); ?>
    <?php endif; ?>

    <button type="submit">Créer</button>
    <p>Vous avez déjà un compte ? <a href="connexion.php">Se Connecter</a></p>
</form>
<?php
require_once 'footer_conn.php';
?>