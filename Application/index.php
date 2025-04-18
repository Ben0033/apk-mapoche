
<?php
require_once 'header.php';
?>
    <section>
        <h1 class="h1">Bienvenue sur MaPoche</h1>
        <div class="diva">
            <p> De nos jours il est essentiel de savoir gérer ses dépenses, c'est dans ce sens que MaPoche est là pour vous aider à suivre vos dépenses au fil du temps.</p>
            <p>MaPoche vous permet d'enregistrer vos dépenses et vos revenus, mais aussi d'en garder une historique </p>
        </div>
        <form id="formulaire">
            <h2>Gérer vos Dépenses et Revenus</h2>
            
            <!-- Input radio pour sélectionner dépense ou revenu -->
            <div class="colone">
            <label>
                <input type="radio" name="type" value="dépense" onclick="afficherCategorie()"> Dépense
            </label>
            <label>
                <input type="radio" name="type" value="revenu" onclick="afficherCategorie()"> Revenu
            </label>
            <br><br>
        </div>
            <!-- Champs texte -->
            <label for="montant">Montant :</label>
            <input type="number" id="montant" name="montant" step="1000" required>
            <br><br>
            
            <label for="description">Description :</label>
            <input type="text-area" id="description" name="description" required>
            <br><br>
            
            <!-- Champ catégorie masqué par défaut -->
           <!-- <label for="categorie">Catégorie :</label> -->
            <input type="text" id="categorie" name="categorie" placeholder="categorie">
            <br><br>
            <div class="btn">
                <button type="submit">Valider</button>
                <button type="reset">supprimer</button>
            </div>
        </form>
    
        <script>
            function afficherCategorie() {
                // Vérifie le bouton radio sélectionné
                const type = document.querySelector('input[name="type"]:checked').value;
                const categorieInput = document.getElementById('categorie');
                
                if (type === 'dépense') {
                    categorieInput.style.display = 'block'; // Affiche l'input catégorie
                } else {
                    categorieInput.style.display = 'none'; // Masque l'input catégorie
                }
            }
        </script>
    </section>
   <?php
   require_once 'footer.php'
   ?>