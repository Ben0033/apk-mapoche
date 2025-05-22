<?php
$title = "Historique";
require_once 'header.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
   header('Location: connexion.php');
   exit;
}

require 'config.php';

// Récupérer les données depuis les tables revenue et depense
$id_user = $_SESSION['id_user'];
$query = $conn->prepare("
    SELECT montant_revenu AS montant, description_revenu AS description, NULL AS categorie, 'Revenu' AS type, date_revenu AS date, id_revenu AS id
    FROM revenue
    WHERE id_user = :id_user
    UNION
    SELECT montant_depense AS montant, description_depense AS description, categorie.nom_cat AS categorie, 'Depense' AS type, date_depense AS date, id_depense AS id
    FROM depense
    INNER JOIN categorie ON categorie.id_cat = depense.id_cat
    WHERE depense.id_user = :id_user
    ORDER BY date DESC
");
$query->execute(['id_user' => $id_user]);
$historique = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table">
   <legend>Historique de vos Dépenses et Revenus</legend>
   <thead class="thead">
      <tr>
         <th>Ordre</th>
         <th>Montant</th>
         <th>Description</th>
         <th>Catégorie</th>
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
               <td><?= htmlspecialchars($entry['description']) ?></td>
               <td>
                  <?php if ($entry['type'] === 'Depense'): ?>
                     <?= htmlspecialchars($entry['categorie']) ?>
                  <?php else: ?>
                     <span class="text-muted">N/A</span>
                  <?php endif; ?>
               </td>  
               <td><?= htmlspecialchars($entry['type']) ?></td>
               <td><?= htmlspecialchars($entry['date']) ?></td>
               <td>
                  <a href="supprimer.php?id=<?= $entry['id'] ?>&type=<?= $entry['type'] ?>" class="btn-danger">Supprimer</a>
                  <a href="modifier.php?id=<?= $entry['id'] ?>&type=<?= $entry['type'] ?>" class="btn-info">Modifier</a>
               </td>
            </tr>
         <?php endforeach; ?>
      <?php else: ?>
         <tr>
            <td colspan="6" style="text-align: center;">Aucun enregistrement trouvé</td>
            <a id="ajouterbtn" href="index.php">Ajouter</a>
         </tr>
      <?php endif; ?>
   </tbody>
</table>

<?php
require_once 'footer.php';
?>