<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireClient();

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$categorie_id = isset($_GET['categorie']) ? intval($_GET['categorie']) : 0;

$sql = "SELECT s.*, u.nom, u.prenom, c.nom_categorie 
        FROM Service s
        JOIN Utilisateur u ON s.id_prestataire = u.id_utilisateur
        JOIN Categorie c ON s.id_categorie = c.id_categorie
        WHERE u.statut_compte = 'actif'";
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

$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Explorer - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .filtre-card {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
        }
        .service-item {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
            transition: all 0.2s;
            height: 100%;
        }
        .service-item:hover {
            border-color: #c17b4c;
            transform: translateY(-3px);
        }
        .btn-demande {
            background: #c17b4c;
            border: none;
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
            color: white;
            transition: all 0.15s;
        }
        .btn-demande:hover {
            background: #a05f38;
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
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">CLIENT</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Explorer les services</h1>
        <p style="color: #8b8a86;">Trouvez le professionnel qu'il vous faut</p>
    </div>
    
    <div class="filtre-card mb-5">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="recherche" class="form-control" style="background: #fefcf8; border: 1px solid #e2dcd0; border-radius: 14px; padding: 12px;" placeholder="🔍 Que recherchez-vous ?" value="<?= htmlspecialchars($recherche) ?>">
            </div>
            <div class="col-md-4">
                <select name="categorie" class="form-select" style="background: #fefcf8; border: 1px solid #e2dcd0; border-radius: 14px; padding: 12px;">
                    <option value="0">📁 Toutes les catégories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>" <?= $categorie_id == $cat['id_categorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" style="background: #c17b4c; border: none; border-radius: 40px; padding: 12px; width: 100%; color: white; font-weight: 500;">
                    <i class="fas fa-search"></i> Filtrer
                </button>
            </div>
        </form>
    </div>
    
    <?php if(count($services) == 0): ?>
        <div class="text-center py-5" style="background: #fffef7; border: 1px solid #e2dcd0; border-radius: 20px;">
            <i class="fas fa-folder-open fa-3x" style="color: #d4cdbe; margin-bottom: 1rem;"></i>
            <p style="color: #8b8a86;">Aucun service trouvé pour le moment.</p>
            <a href="explore.php" style="color: #c17b4c; text-decoration: none;">Réinitialiser les filtres</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($services as $service): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="service-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge-cat"><?= htmlspecialchars($service['nom_categorie']) ?></span>
                            <?php if($service['prix_estime']): ?>
                                <span class="badge-cat prix-badge"><?= number_format($service['prix_estime'], 0, ',', ' ') ?> CFA</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="mb-2" style="font-weight: 600;"><?= htmlspecialchars($service['nom_service']) ?></h5>
                        <p class="text-muted small mb-3"><?= nl2br(htmlspecialchars(substr($service['description_service'], 0, 100))) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="small" style="color: #8b8a86;">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($service['prenom']) ?>
                            </div>
                            <a href="faire_demande.php?service_id=<?= $service['id_service'] ?>" class="btn-demande">
                                📩 Demander
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