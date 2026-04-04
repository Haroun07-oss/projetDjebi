<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Statistiques pour client
if($user_role == 'client') {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_demandes,
            SUM(CASE WHEN statut = 'en attente' THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN statut = 'acceptee' THEN 1 ELSE 0 END) as acceptees,
            SUM(CASE WHEN statut = 'terminee' THEN 1 ELSE 0 END) as terminees,
            SUM(CASE WHEN statut = 'refusee' THEN 1 ELSE 0 END) as refusees
        FROM Demande WHERE id_client = ?
    ");
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch();
}

// Statistiques pour prestataire
if($user_role == 'prestataire') {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_demandes,
            SUM(CASE WHEN statut = 'en attente' THEN 1 ELSE 0 END) as en_attente,
            SUM(CASE WHEN statut = 'acceptee' THEN 1 ELSE 0 END) as acceptees,
            SUM(CASE WHEN statut = 'terminee' THEN 1 ELSE 0 END) as terminees,
            SUM(CASE WHEN statut = 'refusee' THEN 1 ELSE 0 END) as refusees,
            (SELECT COUNT(*) FROM Service WHERE id_prestataire = ?) as total_services,
            (SELECT AVG(note) FROM Avis a JOIN Demande d ON a.id_demande = d.id_demande WHERE d.id_prestataire = ?) as note_moyenne
        FROM Demande WHERE id_prestataire = ?
    ");
    $stmt->execute([$user_id, $user_id, $user_id]);
    $stats = $stmt->fetch();
}

// Statistiques pour admin
if($user_role == 'admin') {
    $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role != 'admin'")->fetchColumn();
    $stats['total_prestataires'] = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role = 'prestataire'")->fetchColumn();
    $stats['total_clients'] = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role = 'client'")->fetchColumn();
    $stats['total_services'] = $pdo->query("SELECT COUNT(*) FROM Service")->fetchColumn();
    $stats['total_demandes'] = $pdo->query("SELECT COUNT(*) FROM Demande")->fetchColumn();
    $stats['demandes_en_attente'] = $pdo->query("SELECT COUNT(*) FROM Demande WHERE statut = 'en attente'")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success">
                <h4><i class="fas fa-chart-line"></i> Bienvenue <?= htmlspecialchars($_SESSION['user_prenom']) ?> !</h4>
                <p>Voici votre tableau de bord personnel.</p>
            </div>
        </div>
    </div>

    <!-- DASHBOARD CLIENT -->
    <?php if($user_role == 'client'): ?>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-2x mb-2"></i>
                    <h3><?= $stats['total_demandes'] ?? 0 ?></h3>
                    <p>Total demandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h3><?= $stats['en_attente'] ?? 0 ?></h3>
                    <p>En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h3><?= $stats['terminees'] ?? 0 ?></h3>
                    <p>Terminées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h3><?= $stats['refusees'] ?? 0 ?></h3>
                    <p>Refusées</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-search"></i> Actions rapides
                </div>
                <div class="card-body">
                    <a href="explorer_services.php" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-search"></i> Rechercher un service
                    </a>
                    <a href="mes_demandes.php" class="btn btn-info w-100">
                        <i class="fas fa-list"></i> Voir mes demandes
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i> Répartition
                </div>
                <div class="card-body">
                    <canvas id="statChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- DASHBOARD PRESTATAIRE -->
    <?php if($user_role == 'prestataire'): ?>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <i class="fas fa-concierge-bell fa-2x mb-2"></i>
                    <h3><?= $stats['total_services'] ?? 0 ?></h3>
                    <p>Mes services</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <h3><?= $stats['total_demandes'] ?? 0 ?></h3>
                    <p>Demandes reçues</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <h3><?= number_format($stats['note_moyenne'] ?? 0, 1) ?> ★</h3>
                    <p>Note moyenne</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-warning">
                    <i class="fas fa-clock"></i> Demandes en attente
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4"><?= $stats['en_attente'] ?? 0 ?></h1>
                    <a href="demandes_recues.php" class="btn btn-warning">Gérer les demandes</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-chart-line"></i> Évolution
                </div>
                <div class="card-body">
                    <canvas id="prestaChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt"></i> Actions rapides
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="proposer_service.php" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Ajouter un service
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="mes_services.php" class="btn btn-info w-100">
                                <i class="fas fa-edit"></i> Gérer mes services
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="demandes_recues.php" class="btn btn-warning w-100">
                                <i class="fas fa-inbox"></i> Voir les demandes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- DASHBOARD ADMIN -->
    <?php if($user_role == 'admin'): ?>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h3><?= $stats['total_users'] ?? 0 ?></h3>
                    <p>Utilisateurs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <i class="fas fa-user-tie fa-2x mb-2"></i>
                    <h3><?= $stats['total_prestataires'] ?? 0 ?></h3>
                    <p>Prestataires</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <i class="fas fa-concierge-bell fa-2x mb-2"></i>
                    <h3><?= $stats['total_services'] ?? 0 ?></h3>
                    <p>Services</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <i class="fas fa-tasks fa-2x mb-2"></i>
                    <h3><?= $stats['total_demandes'] ?? 0 ?></h3>
                    <p>Demandes</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i> Demandes en attente
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4"><?= $stats['demandes_en_attente'] ?? 0 ?></h1>
                    <p>À traiter</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i> Répartition utilisateurs
                </div>
                <div class="card-body">
                    <canvas id="adminChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-cogs"></i> Administration
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="gerer_utilisateurs.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-users-cog"></i> Gérer les utilisateurs
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="gerer_categories.php" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-folder"></i> Gérer les catégories
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
<?php if($user_role == 'client'): ?>
// Graphique pour client
var ctx = document.getElementById('statChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['En attente', 'Acceptées', 'Terminées', 'Refusées'],
        datasets: [{
            data: [<?= $stats['en_attente'] ?? 0 ?>, <?= $stats['acceptees'] ?? 0 ?>, <?= $stats['terminees'] ?? 0 ?>, <?= $stats['refusees'] ?? 0 ?>],
            backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
        }]
    }
});
<?php endif; ?>

<?php if($user_role == 'prestataire'): ?>
// Graphique pour prestataire
var ctx = document.getElementById('prestaChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['En attente', 'Acceptées', 'Terminées', 'Refusées'],
        datasets: [{
            label: 'Nombre de demandes',
            data: [<?= $stats['en_attente'] ?? 0 ?>, <?= $stats['acceptees'] ?? 0 ?>, <?= $stats['terminees'] ?? 0 ?>, <?= $stats['refusees'] ?? 0 ?>],
            backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, stepSize: 1 }
        }
    }
});
<?php endif; ?>

<?php if($user_role == 'admin'): ?>
// Graphique pour admin
var ctx = document.getElementById('adminChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Clients', 'Prestataires'],
        datasets: [{
            data: [<?= $stats['total_clients'] ?? 0 ?>, <?= $stats['total_prestataires'] ?? 0 ?>],
            backgroundColor: ['#0d6efd', '#20c997']
        }]
    }
});
<?php endif; ?>
</script>

</body>
</html>