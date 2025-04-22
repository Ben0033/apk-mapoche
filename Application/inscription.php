<?php
require_once 'header.php';
require 'config.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_var($_POST['email_user'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['mot_de_passe_user'];
    $nom = htmlspecialchars($_POST['nom_user']);
    $prenom = htmlspecialchars($_POST['prenom_user']);
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe_user'];

    // File upload handling
    $photo = $_FILES['photo_user']['name'];
    $photo_tmp = $_FILES['photo_user']['tmp_name'];
    $photo_size = $_FILES['photo_user']['size'];
    $photo_error = $_FILES['photo_user']['error'];
    $photo_ext = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
    $photo_new_name = uniqid('', true) . '.' . $photo_ext;
    $photo_destination = 'uploads/' . $photo_new_name;

    if (in_array($photo_ext, $allowed_ext) && $photo_size <= 2000000 && $photo_error === 0) {
        move_uploaded_file($photo_tmp, $photo_destination);
    } else {
        $message = "Erreur lors de l'upload de la photo";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email_user = :mail");
    $stmt->bindParam(':mail', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $message = "L'email existe déjà";
    } else {
        if (!empty($username) && !empty($password) && !empty($nom) && !empty($prenom) && !empty($photo) && !empty($confirmer_mot_de_passe)) {
            if ($password !== $confirmer_mot_de_passe) {
                $message = "Les mots de passe ne correspondent pas";
            } else {
                $password = password_hash($password, PASSWORD_BCRYPT);

                // Insert user into database
                $stmt = $conn->prepare("INSERT INTO users (email_user, mot_de_passe_user, nom_user, prenom_user, photo_user) VALUES (:mail, :pwd, :nom, :prenom, :photo)");
                $stmt->bindParam(':mail', $username);
                $stmt->bindParam(':pwd', $password);
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':photo', $photo_new_name);

                if ($stmt->execute()) {
                    $message = "Inscription réussie";
                     // Reset form fields
                        $username = '';
                        $password = '';
                        $nom = '';
                        $prenom = '';
                        $confirmer_mot_de_passe = '';
                        $photo = '';
                } else {
                    $message = "Inscription échouée";
                }
            }
        } else {
            $message = "Veuillez remplir tous les champs";
        }
    }
}
?>
<form class="connexion" action="" method="post" enctype="multipart/form-data">
    <input type="text" placeholder="NOM" required="required" name="nom_user">
    <input type="text" placeholder="PRENOM" required="required" name="prenom_user">
    <input type="email" placeholder="Email" required="required" name="email_user">
    <input type="password" placeholder="Mots De Passe" required="required" name="mot_de_passe_user">
    <input type="password" placeholder="Confirmer le mot de passe" required="required" name="confirmer_mot_de_passe_user">
    <input type="file" name="photo_user" required="required" >

    <?php if (!empty($message)): ?>
      <p id="message"><?=$message?></p>
    <?php endif; ?>
    <button type="submit">Créer</button>
    <p>Vous avez déjà un compte ? <a href="connexion.php">Se Connecter</a></p>
</form>
<?php
require_once 'footer.php';
?>