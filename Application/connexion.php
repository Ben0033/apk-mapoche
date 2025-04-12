<?php
   require_once 'header.php'
   ?>
    <form class="connexion" action="">
        <h2>CONNEXION A VOTRE COMPTE</h2>
        
            <input type="email" placeholder="Email" required="required">
       
            <input type="password" placeholder="password" required="required">
            <div class="mtpp">
                <div>
                 <input type="checkbox"> se souvenir de moi 
                 </div>
            
                <a href="#">Mots De Passe Oublier</a>
            </div>
           
       
            <button type="submit">Se Connecter</button>
            
        
        <p>vous navez pas de compte? <a href="inscription.php">creer un compte -></a></p>

    </form>
<?php
   require_once 'footer.php'
   ?>