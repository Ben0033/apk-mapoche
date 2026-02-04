<?php
require_once 'includes/bootstrap.php';

$title = "Historique";
Auth::requireLogin(); // Rediriger si non connecté

require_once 'header.php';

// Récupérer les données depuis les tables revenue et depense
try {
   $historique = Database::getInstance()->fetchAll(
      "SELECT montant_revenu AS montant, description_revenu AS description, NULL AS categorie, 'Revenu' AS type, date_revenu AS date, id_revenu AS id
        FROM revenue
        WHERE id_user = ?
        UNION
        SELECT montant_depense AS montant, description_depense AS description, categorie.nom_cat AS categorie, 'Depense' AS type, date_depense AS date, id_depense AS id
        FROM depense
        INNER JOIN categorie ON categorie.id_cat = depense.id_cat
        WHERE depense.id_user = ?
        ORDER BY date DESC",
      [Auth::userId(), Auth::userId()]
   );
} catch (Exception $e) {
   $historique = [];
   error_log("Error fetching history: " . $e->getMessage());
}
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
               <td data-label="Montant"><?= htmlspecialchars($entry['montant']) ?></td>
               <td data-label="Description"><?= htmlspecialchars($entry['description']) ?></td>
               <td data-label="Catégorie">
                  <?php if ($entry['type'] === 'Depense'): ?>
                     <?= htmlspecialchars($entry['categorie']) ?>
                  <?php else: ?>
                     <span class="text-muted">N/A</span>
                  <?php endif; ?>
               </td>
               <td data-label="Type"><?= htmlspecialchars($entry['type']) ?></td>
               <td data-label="Date"><?= htmlspecialchars($entry['date']) ?></td>
               <td data-label="Actions">
                  <a href="supprimer.php?id=<?= $entry['id'] ?>&type=<?= $entry['type'] ?>" class="btn-danger">Supprimer</a>
                  <a href="modifier.php?id=<?= $entry['id'] ?>&type=<?= $entry['type'] ?>" class="btn-info">Modifier</a>
               </td>
            </tr>
         <?php endforeach; ?>
      <?php else: ?>
         <tr>
            <td colspan="7" style="text-align: center;">
               Aucun enregistrement trouvé
               <a id="ajouterbtn" href="index.php">Ajouter</a>
            </td>
         </tr>
      <?php endif; ?>
   </tbody>
</table>

<?php
require_once 'footer.php';
?>