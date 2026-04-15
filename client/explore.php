<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireClient();

$recherche = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
$categorie_id = isset($_GET['categorie']) ? intval($_GET['categorie']) : 0;
$ville_filtre = isset($_GET['ville']) ? trim($_GET['ville']) : ''; // NOUVEAU : filtre par ville

// Requête modifiée pour inclure la ville et la moyenne des notes
$sql = "SELECT s.*, 
        u.nom, u.prenom, u.ville,
        c.nom_categorie,
        COALESCE(AVG(a.note), 0) as note_moyenne,
        COUNT(a.id_avis) as nombre_avis
        FROM Service s
        JOIN Utilisateur u ON s.id_prestataire = u.id_utilisateur
        JOIN Categorie c ON s.id_categorie = c.id_categorie
        LEFT JOIN Demande d ON s.id_service = d.id_service
        LEFT JOIN Avis a ON d.id_demande = a.id_demande
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
// NOUVEAU : filtre par ville
if(!empty($ville_filtre)) {
    $sql .= " AND u.ville LIKE ?";
    $params[] = "%$ville_filtre%";
}

$sql .= " GROUP BY s.id_service ORDER BY s.id_service DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$services = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();

// NOUVEAU : Récupérer les villes distinctes pour le filtre
$villes = $pdo->query("SELECT DISTINCT ville FROM Utilisateur WHERE role = 'prestataire' AND ville IS NOT NULL AND ville != '' ORDER BY ville")->fetchAll();
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
        /* NOUVEAU STYLES */
        .presta-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0ebe2;
        }
        .presta-ville {
            background: #f0ebe2;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            color: #5c5b58;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .presta-ville i {
            font-size: 0.7rem;
            color: #c17b4c;
        }
        .note-etoiles {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef8e7;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
        }
        .note-etoiles .etoiles {
            color: #d4a02b;
            letter-spacing: 1px;
        }
        .note-etoiles .avis-count {
            color: #8b8a86;
        }
        .filtre-ville {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0ebe2;
        }
        .badge-filtre {
            background: #f0ebe2;
            color: #5c5b58;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }
        .badge-filtre:hover, .badge-filtre.active {
            background: #c17b4c;
            color: white;
        }
        .badge-filtre.active i {
            color: white;
        }
        .reset-filtre {
            color: #b87a5a;
            font-size: 0.7rem;
            text-decoration: none;
            margin-left: 10px;
        }
        .reset-filtre:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">CLIENT</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;"><i class="fas fa-search me-2"></i> Explorer les services</h1>
        <p style="color: #8b8a86;">Trouvez le professionnel qu'il vous faut</p>
    </div>
    
    <div class="filtre-card mb-5">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="recherche" class="form-control" style="background: #fefcf8; border: 1px solid #e2dcd0; border-radius: 14px; padding: 12px;" placeholder="Que recherchez-vous ?" value="<?= htmlspecialchars($recherche) ?>">
            </div>
            <div class="col-md-3">
                <select name="categorie" class="form-select" style="background: #fefcf8; border: 1px solid #e2dcd0; border-radius: 14px; padding: 12px;">
                    <option value="0"><i class="fas fa-folder me-1"></i> Toutes les catégories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>" <?= $categorie_id == $cat['id_categorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="ville" class="form-select" style="background: #fefcf8; border: 1px solid #e2dcd0; border-radius: 14px; padding: 12px;">
                    <option value="">Toutes les villes</option>
                    <?php foreach($villes as $v): ?>
                        <option value="<?= htmlspecialchars($v['ville']) ?>" <?= $ville_filtre == $v['ville'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v['ville']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" style="background: #c17b4c; border: none; border-radius: 40px; padding: 12px; width: 100%; color: white; font-weight: 500;">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
            </div>
        </form>
        
        <!-- NOUVEAU : Filtres rapides par ville -->
        <?php if(count($villes) > 0): ?>
        <div class="filtre-ville">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span style="font-size: 0.7rem; color: #8b8a86;"><i class="fas fa-map-marker-alt me-1"></i> Villes populaires :</span>
                <?php 
                $top_villes = array_slice($villes, 0, 5);
                foreach($top_villes as $v): 
                    $is_active = ($ville_filtre == $v['ville']);
                ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['ville' => $v['ville'], 'page' => 1])) ?>" 
                       class="badge-filtre <?= $is_active ? 'active' : '' ?>">
                        <i class="fas fa-city"></i> <?= htmlspecialchars($v['ville']) ?>
                    </a>
                <?php endforeach; ?>
                <?php if(!empty($ville_filtre)): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['ville' => ''])) ?>" class="reset-filtre">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if(count($services) == 0): ?>
        <div class="text-center py-5" style="background: #fffef7; border: 1px solid #e2dcd0; border-radius: 20px;">
            <i class="fas fa-folder-open fa-3x" style="color: #d4cdbe; margin-bottom: 1rem;"></i>
            <p style="color: #8b8a86;">Aucun service trouvé pour le moment.</p>
            <a href="explore.php" style="color: #c17b4c; text-decoration: none;"><i class="fas fa-sync-alt me-1"></i> Réinitialiser les filtres</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($services as $service): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="service-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge-cat"><i class="fas fa-tag me-1"></i> <?= htmlspecialchars($service['nom_categorie']) ?></span>
                            <?php if($service['prix_estime']): ?>
                                <span class="badge-cat prix-badge"><i class="fas fa-money-bill-wave me-1"></i> <?= number_format($service['prix_estime'], 0, ',', ' ') ?> CFA</span>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="mb-2" style="font-weight: 600;"><?= htmlspecialchars($service['nom_service']) ?></h5>
                        <p class="text-muted small mb-3"><?= nl2br(htmlspecialchars(substr($service['description_service'], 0, 100))) ?>...</p>
                        
                        <!-- NOUVEAU : Infos prestataire (ville + notes) -->
                        <div class="presta-info">
                            <div class="presta-ville">
                                <i class="fas fa-map-marker-alt"></i> <?= !empty($service['ville']) ? htmlspecialchars($service['ville']) : 'Non renseignée' ?>
                            </div>
                            <div class="note-etoiles">
                                <span class="etoiles">
                                    <?php 
                                    $note = round($service['note_moyenne']);
                                    for($i = 1; $i <= 5; $i++): 
                                        echo $i <= $note ? '★' : '☆';
                                    endfor; 
                                    ?>
                                </span>
                                <span class="avis-count">(<?= $service['nombre_avis'] ?> avis)</span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small" style="color: #8b8a86;">
                                <i class="fas fa-user me-1"></i> <?= htmlspecialchars($service['prenom']) ?>
                            </div>
                            <a href="faire_demande.php?service_id=<?= $service['id_service'] ?>" class="btn-demande">
                                <i class="fas fa-paper-plane me-1"></i> Demander
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