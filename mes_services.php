<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'prestataire') {
    header('Location: connexion.php');
    exit();
}

$id_prestataire = $_SESSION['user_id'];

// Récupérer les services du prestataire
$stmt = $pdo->prepare("
    SELECT s.*, c.nom_categorie 
    FROM Service s
    JOIN Categorie c ON s.id_categorie = c.id_categorie
    WHERE s.id_prestataire = ?
    ORDER BY s.id_service DESC
");
$stmt->execute([$id_prestataire]);
$services = $stmt->fetchAll();

// Supprimer un service
if(isset($_GET['supprimer'])) {
    $id_service = intval($_GET['supprimer']);
    $delete = $pdo->prepare("DELETE FROM Service WHERE id_service = ? AND id_prestataire = ?");
    $delete->execute([$id_service, $id_prestataire]);
    header('Location: mes_services.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mes services proposés</h2>
        <a href="proposer_service.php" class="btn btn-success">+ Nouveau service</a>
    </div>

    <?php if(count($services) == 0): ?>
        <div class="alert alert-info">
            Vous n'avez encore proposé aucun service. <a href="proposer_service.php">Ajoutez votre premier service</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($services as $service): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($service['nom_service']) ?></h5>
                            <span class="badge bg-secondary"><?= htmlspecialchars($service['nom_categorie']) ?></span>
                            <?php if($service['prix_estime']): ?>
                                <span class="badge bg-info"><?= $service['prix_estime'] ?> €</span>
                            <?php endif; ?>
                            <p class="card-text mt-2"><?= nl2br(htmlspecialchars(substr($service['description_service'], 0, 100))) ?>...</p>
                            <a href="?supprimer=<?= $service['id_service'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce service ?')">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>