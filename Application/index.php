
<?php
$title = "Accueil";
require_once 'header.php';
// session_start();
// require "config.php"

// $description=$_POST["description"];
// $type=$_POST["depense"];
// $montant=$_POST["montant"];





require 'config.php';
session_start(); // Démarrer la session pour accéder aux données de l'utilisateur connecté

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php'); // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $type = $_POST['type'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $description = $_POST['description'] ?? null;
    $categorie = $_POST['categorie'] ?? null;
    $iduser = $_SESSION['id_user']; // ID de l'utilisateur connecté

    // Validation des données
    if (!empty($type) && !empty($montant) && !empty($description)) {
        try {
            if ($type === 'revenu') {
                // Insérer dans la table revenu
                $stmt = $conn->prepare("INSERT INTO revenue (montant_revenu, description_revenu, id_user) VALUES (:montant, :description, :id_user)");
                $stmt->bindParam(':montant', $montant);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':id_user', $iduser);
            } elseif ($type === 'dépense') {
                // Insérer dans la table depense
                $stmt = $conn->prepare("INSERT INTO depense (montant_depense, description_depense, id_cat, id_user) VALUES (:montant, :description, :categorie, :id_user)");
                $stmt->bindParam(':montant', $montant);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':categorie', $categorie);
                $stmt->bindParam(':id_user', $iduser);
            }

            // Exécuter la requête
            if ($stmt->execute()) {
                $message= "Les informations ont été enregistrées avec succès.";
            } else {
                $message= "Une erreur est survenue lors de l'enregistrement des informations";
            }
        } catch (PDOException $e) {
            echo "<p>Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        }
    } else {
        $message="<p>Veuillez remplir tous les champs requis.</p>";
    }
}


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
           <marquee behavior="ttt" direction="left">
            <p>Vous pouvez ajouter une dépense ou un revenu en remplissant le formulaire ci-dessous, mais Veuillez choisir s'il sagit d'une depense ou un revenue.</p>
           </marquee> 
           

            
            
            <div class="colone">
            <label>
                <input type="radio" name="type" value="dépense" onclick="afficherCategorie()" required> Dépense
            </label>
            <label>
                <input type="radio" name="type" value="revenu" onclick="afficherCategorie()" required> Revenu
            </label>
            <br><br>
        </div>
           
            <!-- <label for="montant">Montant :</label> -->
            <input type="number" id="montant" name="montant" placeholder="Montant" style="display: none;"required>
            <br><br>
            
            <!-- <label for="description">Description :</label> -->
            <input type="text-area" id="description" name="description" placeholder="description" style="display: none;" required>
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
                <button type="reset">supprimer</button>
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
        categorieInput.style.display = 'none';
        montantInput.style.display = 'none';
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