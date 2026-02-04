<?php

/**
 * Classe Auth - Authentification centralisée
 */
class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['id_user']);
    }

    /**
     * Obtenir l'ID de l'utilisateur connecté
     */
    public static function userId()
    {
        return $_SESSION['id_user'] ?? null;
    }

    /**
     * Obtenir les données de l'utilisateur connecté
     */
    public static function user()
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id_user' => $_SESSION['id_user'] ?? null,
            'email_user' => $_SESSION['email_user'] ?? null,
            'nom_user' => $_SESSION['nom_user'] ?? null,
            'prenom_user' => $_SESSION['prenom_user'] ?? null,
            'photo_user' => $_SESSION['photo_user'] ?? null,
        ];
    }

    /**
     * Rediriger si non connecté (pour pages protégées)
     */
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            redirect('connexion.php');
        }
    }

    /**
     * Rediriger si déjà connecté (pour login/register)
     */
    public static function requireLogout()
    {
        if (self::isLoggedIn()) {
            redirect('index.php');
        }
    }

    /**
     * Enregistrer (Register)
     */
    public static function register($email, $password, $nom, $prenom, $photo_path = null)
    {
        $db = Database::getInstance();

        // Valider email
        if (!validateEmail($email)) {
            throw new Exception('Email invalide');
        }

        // Valider long mot de passe
        if (strlen($password) < 8) {
            throw new Exception('Mot de passe: minimum 8 caractères');
        }

        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            throw new Exception('Mot de passe: minimum 1 majuscule et 1 chiffre');
        }

        // Vérifier que l'email n'existe pas
        $existing = $db->fetchOne('SELECT id_user FROM users WHERE email_user = ?', [$email]);
        if ($existing) {
            throw new Exception('Cet email est déjà enregistré');
        }

        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insérer l'utilisateur
        try {
            $db->execute(
                'INSERT INTO users (email_user, mot_de_passe_user, nom_user, prenom_user, photo_user) 
                 VALUES (?, ?, ?, ?, ?)',
                [$email, $hashed_password, $nom, $prenom, $photo_path ?? 'default.png']
            );

            logAction('USER_REGISTERED', ['email' => $email]);
            return true;
        } catch (Exception $e) {
            logAction('USER_REGISTER_FAILED', ['email' => $email, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Connexion (Login)
     */
    public static function login($email, $password)
    {
        $db = Database::getInstance();

        // Récupérer l'utilisateur
        $user = $db->fetchOne(
            'SELECT id_user, mot_de_passe_user, nom_user, prenom_user, photo_user FROM users WHERE email_user = ?',
            [$email]
        );

        if (!$user) {
            logAction('LOGIN_FAILED', ['email' => $email, 'reason' => 'email_not_found']);
            throw new Exception('Email ou mot de passe incorrect');
        }

        // Vérifier le mot de passe
        if (!password_verify($password, $user['mot_de_passe_user'])) {
            logAction('LOGIN_FAILED', ['email' => $email, 'reason' => 'password_mismatch']);
            throw new Exception('Email ou mot de passe incorrect');
        }

        // Créer la session
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['email_user'] = $email;
        $_SESSION['nom_user'] = $user['nom_user'];
        $_SESSION['prenom_user'] = $user['prenom_user'];
        $_SESSION['photo_user'] = $user['photo_user'] ?? 'default.png';

        logAction('USER_LOGGED_IN', ['email' => $email]);
        return true;
    }

    /**
     * Déconnexion (Logout)
     */
    public static function logout()
    {
        $email = $_SESSION['email_user'] ?? 'UNKNOWN';
        logAction('USER_LOGGED_OUT', ['email' => $email]);

        session_destroy();
        redirect('connexion.php');
    }

    /**
     * Changer le mot de passe
     */
    public static function changePassword($currentPassword, $newPassword, $confirmPassword)
    {
        $db = Database::getInstance();

        if (!self::isLoggedIn()) {
            throw new Exception('Non connecté');
        }

        // Valider les mots de passe
        if ($newPassword !== $confirmPassword) {
            throw new Exception('Les mots de passe ne correspondent pas');
        }

        if (strlen($newPassword) < 8) {
            throw new Exception('Minimum 8 caractères');
        }

        if (!preg_match('/[A-Z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            throw new Exception('Minimum 1 majuscule et 1 chiffre');
        }

        // Vérifier le mot de passe actuel
        $user = $db->fetchOne(
            'SELECT mot_de_passe_user FROM users WHERE id_user = ?',
            [self::userId()]
        );

        if (!$user || !password_verify($currentPassword, $user['mot_de_passe_user'])) {
            logAction('PASSWORD_CHANGE_FAILED', ['reason' => 'wrong_current_password']);
            throw new Exception('Mot de passe actuel incorrect');
        }

        // Mettre à jour le mot de passe
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $db->execute(
            'UPDATE users SET mot_de_passe_user = ? WHERE id_user = ?',
            [$hashed, self::userId()]
        );

        logAction('PASSWORD_CHANGED', []);
        return true;
    }

    /**
     * Demander réinitialisation de mot de passe
     */
    public static function requestPasswordReset($email)
    {
        $db = Database::getInstance();

        $user = $db->fetchOne('SELECT id_user FROM users WHERE email_user = ?', [$email]);

        if (!$user) {
            // Ne pas révéler si l'email existe (prévention enumération)
            logAction('PASSWORD_RESET_REQUESTED', ['email' => $email, 'found' => false]);
            return true;
        }

        // Générer token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $db->execute(
            'UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id_user = ?',
            [$token, $expires, $user['id_user']]
        );

        // TODO: Envoyer email avec lien
        logAction('PASSWORD_RESET_TOKEN_GENERATED', ['email' => $email]);

        return $token; // En production, envoyer par email
    }

    /**
     * Réinitialiser avec token
     */
    public static function resetPassword($token, $newPassword, $confirmPassword)
    {
        $db = Database::getInstance();

        if ($newPassword !== $confirmPassword) {
            throw new Exception('Les mots de passe ne correspondent pas');
        }

        if (strlen($newPassword) < 8) {
            throw new Exception('Minimum 8 caractères');
        }

        // Vérifier token
        $user = $db->fetchOne(
            'SELECT id_user FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()',
            [$token]
        );

        if (!$user) {
            logAction('PASSWORD_RESET_FAILED', ['reason' => 'invalid_token']);
            throw new Exception('Token invalide ou expiré');
        }

        // Mettre à jour mot de passe
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $db->execute(
            'UPDATE users SET mot_de_passe_user = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id_user = ?',
            [$hashed, $user['id_user']]
        );

        logAction('PASSWORD_RESET_SUCCESSFUL', []);
        return true;
    }
}
