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
} elseif($user_role == 'prestataire') {
    $stats = $pdo->prepare("SELECT (SELECT COUNT(*) FROM Service WHERE id_prestataire=?) as services, (SELECT COUNT(*) FROM Demande WHERE id_prestataire=? AND statut='en attente') as en_attente");
    $stats->execute([$user_id, $user_id]);
    $stats = $stats->fetch();
} else {
    $stats['users'] = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role != 'admin'")->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="hero">
    <div class="container text-center">
        <h1>Bonjour <?= htmlspecialchars($user_prenom) ?> ! 👋</h1>
        <p>Bienvenue sur votre tableau de bord</p>
    </div>
</div>

<div class="container">
    <div class="row mb-5">
        <?php if($user_role == 'client'): ?>
            <div class="col-md-6">
                <div class="stat-card">
                    <i class="fas fa-tasks text-primary"></i>
                    <h3><?= $stats['total'] ?? 0 ?></h3>
                    <p>Total demandes</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <i class="fas fa-clock text-warning"></i>
                    <h3><?= $stats['en_attente'] ?? 0 ?></h3>
                    <p>En attente</p>
                </div>
            </div>
        <?php elseif($user_role == 'prestataire'): ?>
            <div class="col-md-6">
                <div class="stat-card">
                    <i class="fas fa-box text-primary"></i>
                    <h3><?= $stats['services'] ?? 0 ?></h3>
                    <p>Mes services</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <i class="fas fa-inbox text-warning"></i>
                    <h3><?= $stats['en_attente'] ?? 0 ?></h3>
                    <p>Demandes en attente</p>
                </div>
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <div class="stat-card">
                    <i class="fas fa-users text-primary"></i>
                    <h3><?= $stats['users'] ?? 0 ?></h3>
                    <p>Utilisateurs inscrits</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="fas fa-bolt me-2"></i> Actions rapides
        </div>
        <div class="card-body">
            <div class="row">
                <?php if($user_role == 'client'): ?>
                    <div class="col-md-6"><a href="client/explore.php" class="btn btn-primary w-100">🔍 Explorer</a></div>
                    <div class="col-md-6"><a href="client/mes_demandes.php" class="btn btn-info w-100 text-white">📋 Mes demandes</a></div>
                <?php elseif($user_role == 'prestataire'): ?>
                    <div class="col-md-4"><a href="prestataire/ajouter_service.php" class="btn btn-success w-100">➕ Ajouter</a></div>
                    <div class="col-md-4"><a href="prestataire/mes_services.php" class="btn btn-primary w-100">📦 Mes services</a></div>
                    <div class="col-md-4"><a href="prestataire/demandes_recues.php" class="btn btn-warning w-100">📩 Demandes</a></div>
                <?php else: ?>
                    <div class="col-md-4"><a href="admin/utilisateurs.php" class="btn btn-primary w-100">👥 Utilisateurs</a></div>
                    <div class="col-md-4"><a href="admin/categories.php" class="btn btn-info w-100">📁 Catégories</a></div>
                    <div class="col-md-4"><a href="admin/statistiques.php" class="btn btn-success w-100">📊 Stats</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>