<?php
$title = "Historique";
require_once 'header.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
   header('Location: connexion.php');
   exit;
}

require 'config.php';
<<<<<<< HEAD
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
=======

// Récupérer les données depuis la base de données
$id_user = $_SESSION['id_user'];
$query = $conn->prepare("SELECT * FROM historique WHERE id_user = :id_user ORDER BY date DESC");
$query->execute(['id_user' => $id_user]);
$historique = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table">
   <legend>Historique de vos Dépense et Revenu</legend>
   <thead class="thead">
      <tr>
         <th>Ordre</th>
         <th>Montant</th>
         <th>Note</th>
         <th>Type</th>
         <th>Date</th>
         <th>Actions</th>
      </tr>
   </thead>
   <tbody>
      <?php if (!empty($historique)): ?>
         <?php foreach ($historique as $index => $entry): ?>
            <tr>
               <td><?= $index + 1 ?></td>
               <td><?= htmlspecialchars($entry['montant']) ?></td>
               <td><?= htmlspecialchars($entry['note']) ?></td>
               <td><?= htmlspecialchars($entry['type']) ?></td>
               <td><?= htmlspecialchars($entry['date']) ?></td>
               <td>
                  <a href="supprimer.php?id=<?= $entry['id'] ?>" class="btn-danger">Supprimer</a>
                  <a href="modifier.php?id=<?= $entry['id'] ?>" class="btn-info">Modifier</a>
               </td>
>>>>>>> 04977b29710a5a672ecbd1a9c4c3792a06797107
            </tr>
         <?php endforeach; ?>
      <?php else: ?>
         <tr>
            <td colspan="6" style="text-align: center;">Aucun enregistrement trouvé</td>
         </tr>
      <?php endif; ?>
   </tbody>
</table>

<?php
require_once 'footer.php';
?>