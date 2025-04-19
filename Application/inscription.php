<?php
   require_once 'header.php'
   ?>
     <form class="connexion" action="">

            <input type="text" placeholder="NOM" required="required">
            <input type="text" placeholder="PRENOM" required="required">
            <input type="email" placeholder="Email" required="required">
            <input type="password" placeholder="Mots De Passe" required="required">
            <input type="password" placeholder="Confirmer le mot de passe" required="required">
            

        <button type="submit">Creer</button>
        <p>vous avez deja un compte ? <a href="connexion.php">Se Connecter </a></p>
    </form>
    <?php
   require_once 'footer.php'
   ?>