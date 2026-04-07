<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requirePrestataire();

$stmt = $pdo->prepare("SELECT s.*, c.nom_categorie FROM Service s JOIN Categorie c ON s.id_categorie = c.id_categorie WHERE s.id_prestataire = ? ORDER BY s.id_service DESC");
$stmt->execute([$_SESSION['user_id']]);
$services = $stmt->fetchAll();

if(isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM Service WHERE id_service = ? AND id_prestataire = ?")->execute([$_GET['supprimer'], $_SESSION['user_id']]);
    header('Location: mes_services.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes services - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-4">
        <h2><i class="fas fa-box"></i> Mes services</h2>
        <a href="ajouter_service.php" class="btn btn-success"><i class="fas fa-plus"></i> Ajouter</a>
    </div>
    
    <?php if(count($services) == 0): ?>
        <div class="alert alert-info">Vous n'avez aucun service. <a href="ajouter_service.php">Ajoutez votre premier service</a></div>
    <?php else: ?>
        <div class="row">
            <?php foreach($services as $s): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($s['nom_service']) ?></h5>
                            <span class="badge bg-secondary"><?= htmlspecialchars($s['nom_categorie']) ?></span>
                            <?php if($s['prix_estime']): ?>
                                <span class="badge bg-info"><?= $s['prix_estime'] ?> €</span>
                            <?php endif; ?>
                            <p class="mt-2"><?= nl2br(htmlspecialchars(substr($s['description_service'], 0, 100))) ?>...</p>
                            <a href="?supprimer=<?= $s['id_service'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
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