<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header('Location: connexion.php');
    exit();
}

$categorie_id = isset($_GET['categorie']) ? intval($_GET['categorie']) : 0;
$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';

// Construction de la requête
$sql = "SELECT s.*, u.nom, u.prenom, u.id_utilisateur as presta_id, c.nom_categorie
        FROM Service s
        JOIN Utilisateur u ON s.id_prestataire = u.id_utilisateur
        JOIN Categorie c ON s.id_categorie = c.id_categorie
        WHERE u.statut_compte = 'actif' AND u.role = 'prestataire'";

$params = [];

if($categorie_id > 0) {
    $sql .= " AND s.id_categorie = ?";
    $params[] = $categorie_id;
}

if(!empty($recherche)) {
    $sql .= " AND (s.nom_service LIKE ? OR s.description_service LIKE ?)";
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$sql .= " ORDER BY s.id_service DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll();

// Récupérer les catégories pour le filtre
$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Explorer les services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h2>🔍 Explorer les services</h2>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="recherche" class="form-control" placeholder="Rechercher un service..." value="<?= htmlspecialchars($recherche) ?>">
                </div>
                <div class="col-md-3">
                    <select name="categorie" class="form-select">
                        <option value="0">Toutes les catégories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id_categorie'] ?>" <?= $categorie_id == $cat['id_categorie'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom_categorie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des services -->
    <div class="row">
        <?php if(count($services) == 0): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun service trouvé.</div>
            </div>
        <?php else: ?>
            <?php foreach($services as $service): ?>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($service['nom_service']) ?></h5>
                            <span class="badge bg-secondary"><?= htmlspecialchars($service['nom_categorie']) ?></span>
                            <?php if($service['prix_estime']): ?>
                                <span class="badge bg-info"><?= $service['prix_estime'] ?> € estimé</span>
                            <?php endif; ?>
                            <p class="card-text mt-2"><?= nl2br(htmlspecialchars(substr($service['description_service'], 0, 150))) ?></p>
                            <p class="text-muted small">Proposé par <?= htmlspecialchars($service['prenom']) ?> <?= htmlspecialchars($service['nom']) ?></p>
                            <a href="faire_demande.php?service_id=<?= $service['id_service'] ?>" class="btn btn-primary">📩 Faire une demande</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>