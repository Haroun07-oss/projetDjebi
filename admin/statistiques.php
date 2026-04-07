<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireAdmin();

$total_users = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role != 'admin'")->fetchColumn();
$total_prestataires = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role = 'prestataire'")->fetchColumn();
$total_clients = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role = 'client'")->fetchColumn();
$total_services = $pdo->query("SELECT COUNT(*) FROM Service")->fetchColumn();
$total_demandes = $pdo->query("SELECT COUNT(*) FROM Demande")->fetchColumn();
$demandes_attente = $pdo->query("SELECT COUNT(*) FROM Demande WHERE statut = 'en attente'")->fetchColumn();
$moyenne_notes = $pdo->query("SELECT AVG(note) FROM Avis")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-chart-line"></i> Statistiques globales</h2>
    
    <div class="row mt-4">
        <div class="col-md-3"><div class="stat-card"><i class="fas fa-users"></i><h3><?= $total_users ?></h3><p>Utilisateurs</p></div></div>
        <div class="col-md-3"><div class="stat-card"><i class="fas fa-user-tie"></i><h3><?= $total_prestataires ?></h3><p>Prestataires</p></div></div>
        <div class="col-md-3"><div class="stat-card"><i class="fas fa-concierge-bell"></i><h3><?= $total_services ?></h3><p>Services</p></div></div>
        <div class="col-md-3"><div class="stat-card"><i class="fas fa-tasks"></i><h3><?= $total_demandes ?></h3><p>Demandes</p></div></div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Répartition utilisateurs</div>
                <div class="card-body"><canvas id="userChart" height="200"></canvas></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Demandes par statut</div>
                <div class="card-body"><canvas id="demandeChart" height="200"></canvas></div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning">Note moyenne des prestataires</div>
                <div class="card-body text-center">
                    <h1 class="display-1"><?= number_format($moyenne_notes ?: 0, 1) ?> ★</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('userChart'), { type: 'pie', data: { labels: ['Clients', 'Prestataires'], datasets: [{ data: [<?= $total_clients ?>, <?= $total_prestataires ?>], backgroundColor: ['#0d6efd', '#20c997'] }] } });
new Chart(document.getElementById('demandeChart'), { type: 'bar', data: { labels: ['En attente', 'Acceptées', 'Terminées', 'Refusées'], datasets: [{ label: 'Nombre', data: [<?= $demandes_attente ?>, 0, 0, 0], backgroundColor: '#ffc107' }] } });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>