<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'prestataire') {
    header('Location: connexion.php');
    exit();
}

$id_prestataire = $_SESSION['user_id'];

// Traitement du changement de statut
if(isset($_POST['changer_statut'])) {
    $id_demande = intval($_POST['id_demande']);
    $nouveau_statut = $_POST['nouveau_statut'];
    
    $sql = "UPDATE Demande SET statut = ? WHERE id_demande = ? AND id_prestataire = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nouveau_statut, $id_demande, $id_prestataire]);
    
    header('Location: demandes_recues.php');
    exit();
}

// Récupérer les demandes reçues
$stmt = $pdo->prepare("
    SELECT d.*, 
           u.nom as client_nom, u.prenom as client_prenom, u.telephone as client_tel, u.email as client_email,
           s.nom_service,
           a.id_avis as avis_existe
    FROM Demande d
    JOIN Utilisateur u ON d.id_client = u.id_utilisateur
    JOIN Service s ON d.id_service = s.id_service
    LEFT JOIN Avis a ON d.id_demande = a.id_demande
    WHERE d.id_prestataire = ?
    ORDER BY FIELD(d.statut, 'en attente', 'acceptee', 'terminee', 'refusee'), d.date_demande DESC
");
$stmt->execute([$id_prestataire]);
$demandes = $stmt->fetchAll();

// Statistiques
$stats = $pdo->prepare("
    SELECT statut, COUNT(*) as nb 
    FROM Demande 
    WHERE id_prestataire = ? 
    GROUP BY statut
");
$stats->execute([$id_prestataire]);
$statistiques = $stats->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes reçues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-inbox"></i> Demandes reçues</h2>
    
    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h3><?= $statistiques['en attente'] ?? 0 ?></h3>
                    <p>En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h3><?= $statistiques['acceptee'] ?? 0 ?></h3>
                    <p>Acceptées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h3><?= $statistiques['terminee'] ?? 0 ?></h3>
                    <p>Terminées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body text-center">
                    <h3><?= $statistiques['refusee'] ?? 0 ?></h3>
                    <p>Refusées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des demandes -->
    <?php if(count($demandes) == 0): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Vous n'avez reçu aucune demande pour le moment.
        </div>
    <?php else: ?>
        <?php foreach($demandes as $demande): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>
                        <i class="fas fa-user"></i> <?= htmlspecialchars($demande['client_prenom']) ?> <?= htmlspecialchars($demande['client_nom']) ?>
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
                    <h5 class="card-title">
                        <i class="fas fa-concierge-bell"></i> <?= htmlspecialchars($demande['nom_service']) ?>
                    </h5>
                    <p class="card-text">
                        <strong>Demande :</strong> <?= nl2br(htmlspecialchars($demande['description_besoin'])) ?>
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-calendar"></i> Reçue le <?= date('d/m/Y à H:i', strtotime($demande['date_demande'])) ?>
                    </p>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6><i class="fas fa-phone"></i> Contact client :</h6>
                            <p class="small">
                                Tél : <?= htmlspecialchars($demande['client_tel']) ?><br>
                                Email : <?= htmlspecialchars($demande['client_email']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Formulaire de changement de statut -->
                    <?php if($demande['statut'] != 'terminee' && $demande['statut'] != 'refusee'): ?>
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="id_demande" value="<?= $demande['id_demande'] ?>">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <select name="nouveau_statut" class="form-select">
                                        <?php if($demande['statut'] == 'en attente'): ?>
                                            <option value="acceptee">✅ Accepter</option>
                                            <option value="refusee">❌ Refuser</option>
                                        <?php endif; ?>
                                        <?php if($demande['statut'] == 'acceptee'): ?>
                                            <option value="terminee">✔️ Marquer comme terminée</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" name="changer_statut" class="btn btn-primary btn-sm">Appliquer</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <!-- Lien pour voir l'avis si existant -->
                    <?php if($demande['statut'] == 'terminee' && $demande['avis_existe']): ?>
                        <div class="mt-2">
                            <span class="badge bg-success"><i class="fas fa-star"></i> Avis laissé par le client</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>