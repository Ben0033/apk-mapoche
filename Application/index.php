
<?php
$title = "Accueil";
require_once 'header.php';
?>
    <section>
        <h1 class="h1">Bienvenue sur MaPoche</h1>
        <div class="diva">
            <p> De nos jours il est essentiel de savoir gérer ses dépenses, c'est dans ce sens que MaPoche est là pour vous aider à suivre vos dépenses au fil du temps.</p>
            <p>MaPoche vous permet d'enregistrer vos dépenses et vos revenus, mais aussi d'en garder une historique </p>
        </div>
        <form id="formulaire" method="post" action="index.php" enctype="multipart/form-data">
             <h2>Gérer vos Dépenses et Revenus</h2>
            <h4>Ajouter une Dépense ou un Revenu</h4>
           <div class="scroll-text">
            <p>Veillez cliquer sur le type d'enregistrement que vous désiriez effectuer.</p>
           </div>
           
            <div class="colone">
            <label>
                <input type="radio" name="type" value="dépense" onclick="afficherCategorie()" required> Dépense
            </label>
            <label>
                <input type="radio" name="type" value="revenu" onclick="afficherCategorie()" required> Revenu
            </label>
            <br><br>
        </div>
            <!-- Champs texte -->
            <!-- <label for="montant">Montant :</label> -->
            <input type="number" id="montant" name="montant" placeholder="Montant" style="display: none;" required>
            <br><br>
            
            <!-- <label for="description">Description :</label> -->
            <input type="text-area" id="description" name="description" placeholder="Description" style="display: none;" required>
            <br><br>
            
            <!-- Champ catégorie masqué par défaut -->
           <!-- <label for="categorie">Catégorie :</label> -->
            <!-- <input type="text" id="categorie" name="categorie" placeholder="categorie"> -->
             <select name="categorie" id="cat" width="80%" style="display: none;">
                <option value="transport">transport</option>
                <option value="restoration">restoration</option>
                <option value="Soins_Medicaux"> soins medicaux</option>
                <option value="autre">autre</option>
             </select>
            <br><br>
            <?php if (!empty($message)): ?>
            <p id="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <div class="btn">
                <button type="submit">Valider</button>
                <button type="reset">Vider</button>
            </div>
        </form>
    
        <script>
            function afficherCategorie() {
    // Vérifie le bouton radio sélectionné
    const type = document.querySelector('input[name="type"]:checked');
    const categorieInput = document.getElementById('cat'); // Champ catégorie
    const montantInput = document.getElementById('montant'); // Champ montant
    const descriptionInput = document.getElementById('description'); // Champ description

    if (!type) {
        // Si aucun bouton radio n'est sélectionné, on masque tout
        // contenue.style.display = "none";
        montantInput.style.display = 'none';
        categorieInput.style.display = 'none';
        descriptionInput.style.display = 'none';
        return;
    }

    if (type.value === 'dépense') {
        // Si le type est "dépense", on affiche tout
        categorieInput.style.display = 'block';
        montantInput.style.display = 'block';
        descriptionInput.style.display = 'block';
    } else if (type.value === 'revenu') {
        // Si le type est "revenu", on masque le champ catégorie
        categorieInput.style.display = 'none';
        montantInput.style.display = 'block';
        descriptionInput.style.display = 'block';
    }
    
}
        </script>
    </section>
   <?php
   require_once 'footer.php'
   ?>