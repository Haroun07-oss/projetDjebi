<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireClient();

$stmt = $pdo->prepare("
    SELECT d.*, u.nom as presta_nom, u.prenom as presta_prenom, s.nom_service,
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
    <title>Mes demandes - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-list-alt"></i> Mes demandes</h2>
    
    <?php if(count($demandes) == 0): ?>
        <div class="alert alert-info">Vous n'avez aucune demande. <a href="explore.php">Explorer les services</a></div>
    <?php else: ?>
        <?php foreach($demandes as $d): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <strong><?= htmlspecialchars($d['nom_service']) ?></strong>
                    <span class="badge bg-<?= $d['statut']=='en attente'?'warning':($d['statut']=='acceptee'?'info':($d['statut']=='terminee'?'success':'danger')) ?>">
                        <?= $d['statut'] ?>
                    </span>
                </div>
                <div class="card-body">
                    <p><strong>Votre besoin :</strong> <?= nl2br(htmlspecialchars($d['description_besoin'])) ?></p>
                    <p><strong>Prestataire :</strong> <?= htmlspecialchars($d['presta_prenom']) ?> <?= htmlspecialchars($d['presta_nom']) ?></p>
                    <p class="text-muted">Envoyée le <?= date('d/m/Y H:i', strtotime($d['date_demande'])) ?></p>
                    
                    <?php if($d['statut'] == 'terminee' && !$d['id_avis']): ?>
                        <a href="avis.php?id_demande=<?= $d['id_demande'] ?>" class="btn btn-warning btn-sm">⭐ Laisser un avis</a>
                    <?php endif; ?>
                    
                    <?php if($d['id_avis']): ?>
                        <div class="alert alert-light mt-2">
                            <strong>Votre avis :</strong> <?= str_repeat('★', $d['note']) . str_repeat('☆', 5-$d['note']) ?><br>
                            <?= nl2br(htmlspecialchars($d['commentaire'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>