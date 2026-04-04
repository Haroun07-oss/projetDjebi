<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header('Location: connexion.php');
    exit();
}

$id_client = $_SESSION['user_id'];

// Récupérer les demandes du client
$stmt = $pdo->prepare("
    SELECT d.*, 
           u.nom as presta_nom, u.prenom as presta_prenom,
           s.nom_service,
           a.id_avis, a.note, a.commentaire as avis_commentaire
    FROM Demande d
    JOIN Utilisateur u ON d.id_prestataire = u.id_utilisateur
    JOIN Service s ON d.id_service = s.id_service
    LEFT JOIN Avis a ON d.id_demande = a.id_demande
    WHERE d.id_client = ?
    ORDER BY d.date_demande DESC
");
$stmt->execute([$id_client]);
$demandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes demandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-list-alt"></i> Mes demandes de service</h2>

    <?php if(count($demandes) == 0): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Vous n'avez encore fait aucune demande. 
            <a href="explorer_services.php">Explorer les services</a>
        </div>
    <?php else: ?>
        <?php foreach($demandes as $demande): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>
                        <i class="fas fa-concierge-bell"></i> <?= htmlspecialchars($demande['nom_service']) ?>
                    </strong>
                    <span class="badge 
                        <?= $demande['statut'] == 'en attente' ? 'bg-warning' : '' ?>
                        <?= $demande['statut'] == 'acceptee' ? 'bg-info' : '' ?>
                        <?= $demande['statut'] == 'terminee' ? 'bg-success' : '' ?>
                        <?= $demande['statut'] == 'refusee' ? 'bg-danger' : '' ?>
                    ">
                        <?= $demande['statut'] ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Votre besoin :</strong> <?= nl2br(htmlspecialchars($demande['description_besoin'])) ?>
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-calendar"></i> Envoyée le <?= date('d/m/Y à H:i', strtotime($demande['date_demande'])) ?>
                    </p>
                    
                    <p>
                        <strong>Prestataire :</strong> <?= htmlspecialchars($demande['presta_prenom']) ?> <?= htmlspecialchars($demande['presta_nom']) ?>
                    </p>

                    <!-- Affichage de l'avis si existant -->
                    <?php if($demande['id_avis']): ?>
                        <div class="alert alert-light border mt-2">
                            <strong><i class="fas fa-star text-warning"></i> Votre avis :</strong><br>
                            Note : <?= str_repeat('★', $demande['note']) . str_repeat('☆', 5 - $demande['note']) ?><br>
                            Commentaire : <?= nl2br(htmlspecialchars($demande['avis_commentaire'])) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Lien pour laisser un avis (seulement si terminée et pas encore d'avis) -->
                    <?php if($demande['statut'] == 'terminee' && !$demande['id_avis']): ?>
                        <a href="laisser_avis.php?id_demande=<?= $demande['id_demande'] ?>" class="btn btn-warning btn-sm mt-2">
                            <i class="fas fa-star"></i> Laisser un avis
                        </a>
                    <?php endif; ?>
                    
                    <!-- Message si demande refusée -->
                    <?php if($demande['statut'] == 'refusee'): ?>
                        <div class="mt-2">
                            <a href="faire_demande.php?service_id=<?= $demande['id_service'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-redo"></i> Refaire une demande pour ce service
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>