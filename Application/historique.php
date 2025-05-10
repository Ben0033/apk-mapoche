<?php
$title = "Historique";
   require_once 'header.php';
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
      header('Location: connexion.php');
      exit;
}
require 'config.php';
   ?>
  
   <table class="table">
      <legend>Historique de vos Dépense et Revenu</legend>
      <thead class="thead">
         <tr style="text-align: center;">
            <th>ordre</th>
            <th>Montant</th>
            <th>Note</th>
            <th>Type</th>
            <th>Date</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php //foreach(): ?>
            <tr >
               <td class="case1">1</td>
               <td class="case2">2500</td>
               <td class="case3">hfdbdfffv</td>
               <td class="case4">12200</td>
               <td class="case5">ergggrr</td>
               <td class="case6" id="actions"><a href="" class="btn-info">modifier</a> 
               <a href="" class="btn-danger">supprimer</a></td>
            </tr>
         <?php //endforeach; ?>
      </tbody>
   </table> 
   <?php //else: ?>
      <p style="text-align: center;">Aucun un enregistrement</p>
      <?php //endif; ?>
<?php
   require_once 'footer.php'
 ?>