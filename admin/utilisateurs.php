<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireAdmin();

if(isset($_GET['changer_statut'])) {
    $id = intval($_GET['id']);
    $statut = $_GET['changer_statut'];
    $pdo->prepare("UPDATE Utilisateur SET statut_compte = ? WHERE id_utilisateur = ?")->execute([$statut, $id]);
    header('Location: utilisateurs.php');
    exit();
}

if(isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $pdo->prepare("DELETE FROM Utilisateur WHERE id_utilisateur = ? AND role != 'admin'")->execute([$id]);
    header('Location: utilisateurs.php');
    exit();
}

$users = $pdo->query("SELECT * FROM Utilisateur WHERE role != 'admin' ORDER BY date_inscription DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion utilisateurs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-users-cog"></i> Gestion des utilisateurs</h2>
    
    <table class="table">
        <thead>
            <tr><th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?= $u['id_utilisateur'] ?></td>
                <td><?= htmlspecialchars($u['prenom']) ?> <?= htmlspecialchars($u['nom']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['telephone']) ?></td>
                <td><span class="badge bg-<?= $u['role']=='prestataire'?'info':'secondary' ?>"><?= $u['role'] ?></span></td>
                <td><span class="badge bg-<?= $u['statut_compte']=='actif'?'success':'danger' ?>"><?= $u['statut_compte'] ?></span></td>
                <td>
                    <?php if($u['statut_compte'] == 'actif'): ?>
                        <a href="?changer_statut=inactif&id=<?= $u['id_utilisateur'] ?>" class="btn btn-warning btn-sm">Désactiver</a>
                    <?php else: ?>
                        <a href="?changer_statut=actif&id=<?= $u['id_utilisateur'] ?>" class="btn btn-success btn-sm">Activer</a>
                    <?php endif; ?>
                    <a href="?supprimer=<?= $u['id_utilisateur'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>