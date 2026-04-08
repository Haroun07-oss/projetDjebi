<?php
require_once 'includes/config.php';
require_once 'includes/security.php';
requireLogin();

$user_role = $_SESSION['user_role'];
$user_prenom = $_SESSION['user_prenom'];
$user_id = $_SESSION['user_id'];

if($user_role == 'client') {
    $stats = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN statut='en attente' THEN 1 ELSE 0 END) as en_attente FROM Demande WHERE id_client = ?");
    $stats->execute([$user_id]);
    $stats = $stats->fetch();
    
    $lastDemandes = $pdo->prepare("SELECT d.*, s.nom_service FROM Demande d JOIN Service s ON d.id_service = s.id_service WHERE d.id_client = ? ORDER BY d.date_demande DESC LIMIT 3");
    $lastDemandes->execute([$user_id]);
    $lastDemandes = $lastDemandes->fetchAll();
} elseif($user_role == 'prestataire') {
    $stats = $pdo->prepare("SELECT (SELECT COUNT(*) FROM Service WHERE id_prestataire=?) as services, (SELECT COUNT(*) FROM Demande WHERE id_prestataire=? AND statut='en attente') as en_attente");
    $stats->execute([$user_id, $user_id]);
    $stats = $stats->fetch();
    
    $lastDemandes = $pdo->prepare("SELECT d.*, u.prenom, u.nom, s.nom_service FROM Demande d JOIN Utilisateur u ON d.id_client = u.id_utilisateur JOIN Service s ON d.id_service = s.id_service WHERE d.id_prestataire = ? ORDER BY d.date_demande DESC LIMIT 3");
    $lastDemandes->execute([$user_id]);
    $lastDemandes = $lastDemandes->fetchAll();
} else {
    $stats['users'] = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role != 'admin'")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-dash {
            background: #f5efe7;
            padding: 45px 0;
            border-bottom: 1px solid #e2dcd0;
        }
        .stat-dash {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
            transition: all 0.2s;
        }
        .stat-dash:hover {
            border-color: #c17b4c;
        }
        .stat-dash i {
            font-size: 1.8rem;
            color: #c17b4c;
            opacity: 0.7;
        }
        .stat-dash h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0.5rem 0 0;
        }
        .carte-dash {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
        }
        .btn-rapide {
            background: #f0ebe2;
            border: 1px solid #e2dcd0;
            border-radius: 40px;
            padding: 12px;
            text-align: center;
            transition: all 0.2s;
            display: block;
            text-decoration: none;
            color: #2c2b28;
        }
        .btn-rapide:hover {
            background: #fffef7;
            border-color: #c17b4c;
            transform: translateY(-2px);
        }
        .btn-rapide i {
            font-size: 1.3rem;
            color: #c17b4c;
            margin-bottom: 6px;
            display: block;
        }
        .activite-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0ebe2;
        }
        .activite-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include 'includes/navbar.php'; ?>

<div class="hero-dash">
    <div class="container">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">TABLEAU DE BORD</p>
        <h1 style="font-size: 2rem; font-weight: 600; color: #2c2b28;">Salut <?= htmlspecialchars($user_prenom) ?> 👋</h1>
        <p style="color: #8b8a86;">Voici où en est votre activité</p>
    </div>
</div>

<div class="container mt-4">
    <div class="row g-4 mb-5">
        <?php if($user_role == 'client'): ?>
            <div class="col-md-6">
                <div class="stat-dash">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3><?= $stats['total'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.75rem; color: #8b8a86;">Demandes envoyées</p>
                        </div>
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-dash">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3><?= $stats['en_attente'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.75rem; color: #8b8a86;">En attente de réponse</p>
                        </div>
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        <?php elseif($user_role == 'prestataire'): ?>
            <div class="col-md-6">
                <div class="stat-dash">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3><?= $stats['services'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.75rem; color: #8b8a86;">Services proposés</p>
                        </div>
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-dash">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3><?= $stats['en_attente'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.75rem; color: #8b8a86;">Demandes à traiter</p>
                        </div>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <div class="stat-dash">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3><?= $stats['users'] ?? 0 ?></h3>
                            <p style="margin: 0; font-size: 0.75rem; color: #8b8a86;">Utilisateurs inscrits</p>
                        </div>
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="carte-dash mb-5">
        <div style="font-weight: 600; margin-bottom: 1rem; border-left: 3px solid #c17b4c; padding-left: 12px;">
            <i class="fas fa-bolt me-2" style="color: #c17b4c;"></i> Accès rapide
        </div>
        <div class="row g-3">
            <?php if($user_role == 'client'): ?>
                <div class="col-sm-6"><a href="client/explore.php" class="btn-rapide"><i class="fas fa-search"></i> Explorer des services</a></div>
                <div class="col-sm-6"><a href="client/mes_demandes.php" class="btn-rapide"><i class="fas fa-list-alt"></i> Mes demandes</a></div>
            <?php elseif($user_role == 'prestataire'): ?>
                <div class="col-sm-4"><a href="prestataire/ajouter_service.php" class="btn-rapide"><i class="fas fa-plus"></i> Nouveau service</a></div>
                <div class="col-sm-4"><a href="prestataire/mes_services.php" class="btn-rapide"><i class="fas fa-box"></i> Mes services</a></div>
                <div class="col-sm-4"><a href="prestataire/demandes_recues.php" class="btn-rapide"><i class="fas fa-envelope"></i> Demandes reçues</a></div>
            <?php else: ?>
                <div class="col-sm-4"><a href="admin/utilisateurs.php" class="btn-rapide"><i class="fas fa-users"></i> Utilisateurs</a></div>
                <div class="col-sm-4"><a href="admin/categories.php" class="btn-rapide"><i class="fas fa-tags"></i> Catégories</a></div>
                <div class="col-sm-4"><a href="admin/statistiques.php" class="btn-rapide"><i class="fas fa-chart-simple"></i> Statistiques</a></div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if(isset($lastDemandes) && count($lastDemandes) > 0): ?>
        <div class="carte-dash">
            <div style="font-weight: 600; margin-bottom: 1rem; border-left: 3px solid #c17b4c; padding-left: 12px;">
                <i class="fas fa-history me-2" style="color: #c17b4c;"></i> Dernières activités
            </div>
            <?php foreach($lastDemandes as $demande): ?>
                <div class="activite-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($demande['nom_service']) ?></strong>
                        <div class="small text-muted">
                            <?php if($user_role == 'client'): ?>
                                Demande envoyée le <?= date('d/m/Y', strtotime($demande['date_demande'])) ?>
                            <?php else: ?>
                                De <?= htmlspecialchars($demande['prenom']) ?> <?= htmlspecialchars($demande['nom']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="badge" style="background: #f0ebe2; color: #8b8a86; padding: 4px 10px;">
                        <?= $demande['statut'] == 'en attente' ? '⏳ En attente' : ($demande['statut'] == 'acceptee' ? '✅ Acceptée' : ($demande['statut'] == 'terminee' ? '✨ Terminée' : '❌ Refusée')) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>