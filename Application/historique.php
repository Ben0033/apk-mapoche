<?php
$title = "Historique";
require_once 'header.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
   header('Location: connexion.php');
   exit;
}

require 'config.php';

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