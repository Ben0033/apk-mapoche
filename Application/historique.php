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
         <tr>
            <th>ordre</th>
            <th>Montant</th>
            <th>Note</th>
            <th>Type</th>
            <th>Date</th>
            <th>Actions</th>
         </tr>
      </thead>
   </table>
   <table>
      <tbody>
         <?php //foreach(): ?>
            <tr>
               <td>1</td>
               <td>2500</td>
               <td>hfdbdfffv</td>
               <td>12200</td>
               <td>ergggrr</td>
               <td> <a href="" class="btn-danger">supprimer</a> </td>
               <td> <a href="" class="btn-info">modifier</a> </td>
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