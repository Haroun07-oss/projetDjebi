<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requirePrestataire();

// Récupérer les messages flash
$message_succes = $_SESSION['message_succes'] ?? '';
$message_erreur = $_SESSION['message_erreur'] ?? '';
unset($_SESSION['message_succes']);
unset($_SESSION['message_erreur']);

// Récupérer les services
$stmt = $pdo->prepare("SELECT s.*, c.nom_categorie FROM Service s JOIN Categorie c ON s.id_categorie = c.id_categorie WHERE s.id_prestataire = ? ORDER BY s.id_service DESC");
$stmt->execute([$_SESSION['user_id']]);
$services = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes services - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .carte-service-presta {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
            transition: all 0.2s;
            height: 100%;
        }
        .carte-service-presta:hover {
            border-color: #c17b4c;
            transform: translateY(-3px);
        }
        .badge-cat {
            background: #f0ebe2;
            color: #8b8a86;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
        }
        .prix-badge {
            background: #fff0e6;
            color: #c17b4c;
            font-weight: 600;
        }
        .btn-suppr-service {
            background: transparent;
            border: 1px solid #f0ebe2;
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 0.75rem;
            color: #b87a5a;
            transition: all 0.15s;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-suppr-service:hover {
            background: #f5e8e5;
            border-color: #b87a5a;
        }
        .btn-ajout-header {
            background: #c17b4c;
            color: white;
            border-radius: 40px;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.15s;
            display: inline-block;
        }
        .btn-ajout-header:hover {
            background: #a05f38;
            color: white;
        }
        .alerte-succes {
            background: #e8f0ec;
            border-left: 3px solid #7c9c8e;
            padding: 12px 16px;
            border-radius: 12px;
            color: #5c7c6e;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
        .alerte-erreur {
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
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">PRESTATAIRE</p>
            <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Mes services</h1>
            <p style="color: #8b8a86;"><?= count($services) ?> service<?= count($services) > 1 ? 's' : '' ?> proposé<?= count($services) > 1 ? 's' : '' ?></p>
        </div>
        <a href="ajouter_service.php" class="btn-ajout-header">
            <i class="fas fa-plus-circle me-2"></i> Nouveau service
        </a>
    </div>
    
    <?php if($message_succes): ?>
        <div class="alerte-succes">
            <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($message_succes) ?>
        </div>
    <?php endif; ?>
    
    <?php if($message_erreur): ?>
        <div class="alerte-erreur">
            <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($message_erreur) ?>
        </div>
    <?php endif; ?>
    
    <?php if(count($services) == 0): ?>
        <div class="text-center py-5" style="background: #fffef7; border: 1px solid #e2dcd0; border-radius: 20px;">
            <i class="fas fa-box-open fa-3x" style="color: #d4cdbe; margin-bottom: 1rem;"></i>
            <p style="color: #8b8a86;">Vous n'avez aucun service pour le moment.</p>
            <a href="ajouter_service.php" style="background: #c17b4c; color: white; border-radius: 40px; padding: 8px 20px; text-decoration: none; display: inline-block; margin-top: 10px;"><i class="fas fa-plus me-2"></i>Ajouter mon premier service</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($services as $s): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="carte-service-presta">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge-cat"><i class="fas fa-tag me-1"></i> <?= htmlspecialchars($s['nom_categorie']) ?></span>
                            <?php if($s['prix_estime']): ?>
                                <span class="badge-cat prix-badge"><i class="fas fa-money-bill-wave me-1"></i> <?= number_format($s['prix_estime'], 0, ',', ' ') ?> CFA</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="mb-2" style="font-weight: 600;"><?= htmlspecialchars($s['nom_service']) ?></h5>
                        <p class="text-muted small mb-3"><?= nl2br(htmlspecialchars(substr($s['description_service'], 0, 100))) ?>...</p>
                        <div class="d-flex justify-content-end">
                            <!-- Bouton qui redirige vers test_delete.php -->
                            <a href="test_delete.php?service_id=<?= $s['id_service'] ?>" 
                               class="btn-suppr-service">
                                <i class="fas fa-trash-alt me-1"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>