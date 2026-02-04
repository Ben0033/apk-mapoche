<?php
require_once 'includes/bootstrap.php';

Auth::requireLogin();

// Récupérer les paramètres depuis l'URL
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if ($id && $type) {
    try {
        // Valider les paramètres
        if (!validatePositiveInt($id)) {
            throw new Exception("ID invalide");
        }
        
        if (!in_array($type, ['Revenu', 'Depense'])) {
            throw new Exception("Type invalide");
        }

        // Vérifier que la transaction appartient bien à l'utilisateur
        if ($type === 'Revenu') {
            $check = Database::getInstance()->fetch(
                "SELECT id_revenu FROM revenue WHERE id_revenu = ? AND id_user = ?",
                [$id, Auth::userId()]
            );
            
            if (!$check) {
                throw new Exception("Transaction non trouvée");
            }
            
            Database::getInstance()->execute(
                "DELETE FROM revenue WHERE id_revenu = ? AND id_user = ?",
                [$id, Auth::userId()]
            );
            
            logAction('REVENUE_DELETED', ['id' => $id]);
        } elseif ($type === 'Depense') {
            $check = Database::getInstance()->fetch(
                "SELECT id_depense FROM depense WHERE id_depense = ? AND id_user = ?",
                [$id, Auth::userId()]
            );
            
            if (!$check) {
                throw new Exception("Transaction non trouvée");
            }
            
            Database::getInstance()->execute(
                "DELETE FROM depense WHERE id_depense = ? AND id_user = ?",
                [$id, Auth::userId()]
            );
            
            logAction('EXPENSE_DELETED', ['id' => $id]);
        }

        // Rediriger vers l'historique avec un message de succès
        $_SESSION['success_message'] = 'Transaction supprimée avec succès!';
        header('Location: historique.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: historique.php');
        exit;
    }
} else {
    $_SESSION['error_message'] = 'Paramètres invalides';
    header('Location: historique.php');
    exit;
}
?>
