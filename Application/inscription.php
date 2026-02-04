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
<div class="auth-container">
    <div class="auth-card">
        <!-- Logo et Titre -->
        <div class="auth-header">
            <div class="auth-logo">
                <img src="images/wallet.jpg" alt="MaPoche Logo">
            </div>
            <h1 class="auth-title">Créer un compte</h1>
            <p class="auth-subtitle">Rejoignez MaPoche dès maintenant</p>
        </div>

        <!-- Formulaire d'inscription -->
        <form class="auth-form" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken() ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom_user" class="form-label">👤 Nom</label>
                    <input type="text" id="nom_user" name="nom_user" class="form-input" placeholder="Votre nom" required value="<?= sanitize($_POST['nom_user'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="prenom_user" class="form-label">👤 Prénom</label>
                    <input type="text" id="prenom_user" name="prenom_user" class="form-input" placeholder="Votre prénom" required value="<?= sanitize($_POST['prenom_user'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email_user" class="form-label">📧 Email</label>
                <input type="email" id="email_user" name="email_user" class="form-input" placeholder="votre@email.com" required value="<?= sanitize($_POST['email_user'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="mot_de_passe_user" class="form-label">🔒 Mot de passe</label>
                    <input type="password" id="mot_de_passe_user" name="mot_de_passe_user" class="form-input" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label for="confirmer_mot_de_passe_user" class="form-label">🔒 Confirmer</label>
                    <input type="password" id="confirmer_mot_de_passe_user" name="confirmer_mot_de_passe_user" class="form-input" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group">
                <label for="photo_user" class="form-label">📷 Photo de profil</label>
                <div class="upload-area">
                    <input type="file" id="photo_user" name="photo_user" accept="image/*" required>
                    <label for="photo_user" class="upload-label">
                        <span class="upload-icon">📷</span>
                        <span class="upload-text">Choisir une photo</span>
                    </label>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="message-container">
                    <?= ($message_type === 'success') ? displaySuccess($message) : displayError($message) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-primary btn-full">🚀 Créer mon compte</button>
        </form>

        <!-- Lien vers connexion -->
        <div class="auth-footer">
            <p class="auth-switch">
                Vous avez déjà un compte ? 
                <a href="connexion.php" class="auth-link">Se Connecter</a>
            </p>
        </div>
    </div>

    <!-- Features -->
    <div class="auth-features">
        <div class="feature-item">
            <div class="feature-icon">💰</div>
            <h3>Suivi des dépenses</h3>
            <p>Enregistrez facilement vos dépenses quotidiennes</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">📊</div>
            <h3>Statistiques</h3>
            <p>Visualisez vos finances avec des graphiques</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">📱</div>
            <h3>Mobile</h3>
            <p>Interface adaptée pour tous vos appareils</p>
        </div>
    </div>
</div>

<script>
// Animation du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const authCard = document.querySelector('.auth-card');
    const authFeatures = document.querySelectorAll('.feature-item');
    
    // Animation d'entrée
    setTimeout(() => {
        authCard.style.opacity = '1';
        authCard.style.transform = 'translateY(0)';
    }, 100);
    
    // Animation des features
    authFeatures.forEach((feature, index) => {
        setTimeout(() => {
            feature.style.opacity = '1';
            feature.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
});

// Preview de la photo avant upload
document.getElementById('photo_user').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const label = document.querySelector('.upload-label');
    
    if (file) {
        label.querySelector('.upload-text').textContent = file.name;
        label.classList.add('has-file');
    } else {
        label.querySelector('.upload-text').textContent = 'Choisir une photo';
        label.classList.remove('has-file');
    }
});

// Focus sur le premier champ
document.getElementById('nom_user')?.focus();
</script>

<?php
require_once 'footer_conn.php';
?>
