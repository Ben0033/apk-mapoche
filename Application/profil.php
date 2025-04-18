<?php
   require_once 'header.php';
   $nom="Jean";
   $prnom="Dupont";
   ?>
   <div class="img_profil">
      <img src="images/pp.jpg" alt="image de profil">
      <h2><?=$nom ." ". $prnom ?></h2>
      
      <a href="logout.php">Se deconnecter</a>
   </div>
   
<?php
   require_once 'footer.php'
   ?>