<?php
$title = "Historique";
   require_once 'header.php';
   
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
               <td> </td>
               <td> </td>
               <td></td>
               <td> </td>
               <td></td>
               <td> <a href="" class="btn-danger">supprimer</a> </td>
               <td> <a href="" class="btn-info">modifier</a> </td>
            </tr>
         <?php //endforeach; ?>
      </tbody>
   </table> 
   <?php //else: ?>
      <p>Aucun un enregistrement</p>
      <?php //endif; ?>
<?php
   require_once 'footer.php'
 ?>