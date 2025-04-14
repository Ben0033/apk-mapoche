<?php
    require_once 'header.php'
    // session_start();
    // require 'config.php';
    // $message= '';
    // if ($_SERVER['REQUEST_METHOD']=== 'POST') {
    //     $username = $_POST['mail'];
    //     $password = $_POST['pwd'];

    //         $stmt = $conn->prepare("SELECT * from user where email = :mail");
    //         $stmt->bindParam(':mail', $username);
    //         $stmt->execute();
            
    //         $user = $stmt -> fetch(PDO::FETCH_ASSOC);
    //         if  ($user && password_verify($password,$user['passeword']))
    //         {
    //             $_SESSION['iduser'] = $user['idUsers'];
    //             $_SESSION['email'] = $user['LOGIN'];
            
    //             header('LOCATION: index.php');
    //             exit;
    //         }else{
    //             $message = "nom d'utilisation ou mot de passe incorrect";
    //         }
    //     }
?>
    <form class="connexion" action="">
        <h2>CONNEXION A VOTRE COMPTE</h2>
        
            <input type="email" name="mail" placeholder="Email" required="required">
       
            <input type="password" name="pwd" placeholder="password" required="required">
            <div class="mtpp">
                <div>
                 <input type="checkbox"> se souvenir de moi 
                 </div>
            
                <a href="#">Mots De Passe Oublier</a>
            </div>
           
       
            <button type="submit">Se Connecter</button>
            
        
        <p>vous navez pas de compte? <a href="inscription.php">Creer un compte</a></p>

    </form>
<?php
   require_once 'footer.php'
   ?>