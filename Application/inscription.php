<?php
   require_once 'header.php'
   ?>
    <section>
        
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
    

    </section>
    <?php
   require_once 'footer.php'
   ?>