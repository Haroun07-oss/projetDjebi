<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requirePrestataire();

$message = '';
$service = null;
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

if($service_id > 0) {
    // Récupérer les infos du service
    $stmt = $pdo->prepare("SELECT s.*, c.nom_categorie FROM Service s JOIN Categorie c ON s.id_categorie = c.id_categorie WHERE s.id_service = ? AND s.id_prestataire = ?");
    $stmt->execute([$service_id, $_SESSION['user_id']]);
    $service = $stmt->fetch();
}

// Traitement de la confirmation de suppression
if(isset($_POST['confirmer'])) {
    $id = intval($_POST['service_id']);
    
    // Supprimer les demandes liées
    $delDemandes = $pdo->prepare("DELETE FROM Demande WHERE id_service = ?");
    $delDemandes->execute([$id]);
    
    // Supprimer le service
    $delService = $pdo->prepare("DELETE FROM Service WHERE id_service = ? AND id_prestataire = ?");
    if($delService->execute([$id, $_SESSION['user_id']])) {
        $_SESSION['message_succes'] = "Service supprimé avec succès !";
        header('Location: mes_services.php');
        exit();
    } else {
        $message = "Erreur lors de la suppression";
    }
}

// Annulation
if(isset($_POST['annuler'])) {
    header('Location: mes_services.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un service - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f4f1ea;
            font-family: 'Inter', sans-serif;
        }
        .carte-confirmation {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 2rem;
            max-width: 500px;
            margin: 50px auto;
            text-align: center;
        }
        .btn-danger {
            background: #b87a5a;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            color: white;
            transition: all 0.15s;
        }
        .btn-danger:hover {
            background: #a05f38;
        }
        .btn-secondary {
            background: #f0ebe2;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            color: #5c5b58;
            transition: all 0.15s;
        }
        .btn-secondary:hover {
            background: #e2dcd0;
        }
        .service-name {
            background: #faf8f5;
            padding: 15px;
            border-radius: 16px;
            margin: 20px 0;
            font-weight: 600;
            color: #c17b4c;
        }
        .alert-erreur {
            background: #f5e8e5;
            border-left: 3px solid #b87a5a;
            padding: 12px 16px;
            border-radius: 12px;
            color: #b87a5a;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="carte-confirmation">
        <i class="fas fa-trash-alt fa-3x" style="color: #b87a5a; margin-bottom: 20px;"></i>
        
        <h2 style="font-weight: 600;">Confirmer la suppression</h2>
        
        <?php if($message): ?>
            <div class="alert-erreur">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if($service): ?>
            <p>Êtes-vous sûr de vouloir supprimer ce service ?</p>
            <div class="service-name">
                <i class="fas fa-concierge-bell me-2"></i> <?= htmlspecialchars($service['nom_service']) ?>
                <br>
                <small class="text-muted">Catégorie : <?= htmlspecialchars($service['nom_categorie']) ?></small>
            </div>
            <p class="text-muted small">⚠️ Cette action est irréversible. Toutes les demandes liées à ce service seront également supprimées.</p>
            
            <form method="POST" class="mt-4">
                <input type="hidden" name="service_id" value="<?= $service['id_service'] ?>">
                <div class="d-flex gap-3 justify-content-center">
                    <button type="submit" name="annuler" class="btn-secondary">
                        <i class="fas fa-times me-2"></i> Annuler
                    </button>
                    <button type="submit" name="confirmer" class="btn-danger">
                        <i class="fas fa-trash-alt me-2"></i> Supprimer définitivement
                    </button>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted">Service non trouvé ou vous n'êtes pas autorisé.</p>
            <a href="mes_services.php" class="btn-secondary mt-3 d-inline-block">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>