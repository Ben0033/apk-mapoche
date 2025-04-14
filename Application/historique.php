<?php
   require_once 'header.php'
   ?>
   <table>
      <legend>Historique de vos Dépense et Revenu</legend>
      <thead>
         <tr>
            <th>ordre</th>
            <th>Montant</th>
            <th>Note</th>
            <th>Type</th>
            <th>Date</th>
         </tr>
      </thead>
   </table>
   <table>
      <tbody>
         <?php //foreach(): ?>
            <tr>
               <td> <?=htmlspecialchars()?></td>
               <td> <?=htmlspecialchars()?></td>
               <td> <?=htmlspecialchars()?></td>
               <td> <?=htmlspecialchars()?></td>
               <td> <?=htmlspecialchars()?></td>
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