<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireClient();

$stmt = $pdo->prepare("
    SELECT d.*, 
           u.nom as presta_nom, u.prenom as presta_prenom, u.ville as presta_ville,
           s.nom_service,
           a.id_avis, a.note, a.commentaire
    FROM Demande d
    JOIN Utilisateur u ON d.id_prestataire = u.id_utilisateur
    JOIN Service s ON d.id_service = s.id_service
    LEFT JOIN Avis a ON d.id_demande = a.id_demande
    WHERE d.id_client = ?
    ORDER BY d.date_demande DESC
");
$stmt->execute([$_SESSION['user_id']]);
$demandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes demandes - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .demande-card {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
            margin-bottom: 1.2rem;
            transition: all 0.2s;
        }
        .demande-card:hover {
            border-color: #c17b4c;
        }
        .statut-badge {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .statut-attente { background: #f0ebe2; color: #b5a88e; }
        .statut-acceptee { background: #e8f0ec; color: #7c9c8e; }
        .statut-terminee { background: #e8f0ec; color: #7c9c8e; }
        .statut-refusee { background: #f5e8e5; color: #b87a5a; }
        .besoin-box {
            background: #faf8f5;
            border-radius: 14px;
            padding: 12px;
            margin: 12px 0;
        }
        .btn-avis {
            background: #f0ebe2;
            border: none;
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 0.75rem;
            color: #c17b4c;
            transition: all 0.15s;
        }
        .btn-avis:hover {
            background: #c17b4c;
            color: white;
        }
        .etoiles {
            color: #d4a02b;
            font-size: 0.8rem;
            letter-spacing: 2px;
        }
        .presta-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .presta-ville {
            background: #f0ebe2;
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .presta-ville i {
            color: #c17b4c;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">CLIENT</p>
            <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Mes demandes</h1>
            <p style="color: #8b8a86;">Suivez l'état de vos demandes de service</p>
        </div>
        <a href="explore.php" style="background: #c17b4c; color: white; border-radius: 40px; padding: 10px 20px; text-decoration: none; font-size: 0.85rem;">
            <i class="fas fa-plus me-2"></i> Nouvelle demande
        </a>
    </div>
    
    <?php if(count($demandes) == 0): ?>
        <div class="text-center py-5" style="background: #fffef7; border: 1px solid #e2dcd0; border-radius: 20px;">
            <i class="fas fa-inbox fa-3x" style="color: #d4cdbe; margin-bottom: 1rem;"></i>
            <p style="color: #8b8a86;">Vous n'avez pas encore fait de demande.</p>
            <a href="explore.php" style="background: #c17b4c; color: white; border-radius: 40px; padding: 8px 20px; text-decoration: none; display: inline-block; margin-top: 10px;">Explorer les services</a>
        </div>
    <?php else: ?>
        <?php foreach($demandes as $d): ?>
            <div class="demande-card">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1" style="font-weight: 600;"><?= htmlspecialchars($d['nom_service']) ?></h5>
                        <div class="small" style="color: #8b8a86;">
                            <i class="fas fa-user me-1"></i> Prestataire : <?= htmlspecialchars($d['presta_prenom']) ?> <?= htmlspecialchars($d['presta_nom']) ?>
                        </div>
                        <!-- NOUVEAU : Ville du prestataire -->
                        <div class="presta-info">
                            <div class="presta-ville">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= !empty($d['presta_ville']) ? htmlspecialchars($d['presta_ville']) : 'Non renseignée' ?>
                            </div>
                        </div>
                    </div>
                    <span class="statut-badge <?= $d['statut']=='en attente'?'statut-attente':($d['statut']=='acceptee'?'statut-acceptee':($d['statut']=='terminee'?'statut-terminee':'statut-refusee')) ?>">
                        <?= $d['statut'] == 'en attente' ? '<i class="fas fa-hourglass-half me-1"></i> En attente' : ($d['statut'] == 'acceptee' ? '<i class="fas fa-check-circle me-1"></i> Acceptée' : ($d['statut'] == 'terminee' ? '<i class="fas fa-check-double me-1"></i> Terminée' : '<i class="fas fa-times-circle me-1"></i> Refusée')) ?>
                    </span>
                </div>
                
                <div class="besoin-box">
                    <div class="small" style="color: #8b8a86; margin-bottom: 5px;"><i class="fas fa-pencil-alt me-1"></i> Votre besoin :</div>
                    <p class="mb-0" style="font-size: 0.85rem;"><?= nl2br(htmlspecialchars($d['description_besoin'])) ?></p>
                </div>
                
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small" style="color: #b5a88e;">
                        <i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y à H:i', strtotime($d['date_demande'])) ?>
                    </div>
                    
                    <?php if($d['statut'] == 'terminee' && !$d['id_avis']): ?>
                        <a href="avis.php?id_demande=<?= $d['id_demande'] ?>" class="btn-avis">
                            <i class="fas fa-star me-1"></i> Laisser un avis
                        </a>
                    <?php endif; ?>
                </div>
                
                <?php if($d['id_avis']): ?>
                    <div class="mt-3 pt-2" style="border-top: 1px solid #f0ebe2;">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small fw-bold">Votre avis :</span>
                            <span class="etoiles">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <?= $i <= $d['note'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </span>
                        </div>
                        <p class="small mt-1 mb-0" style="color: #8b8a86;">"<?= nl2br(htmlspecialchars($d['commentaire'])) ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>