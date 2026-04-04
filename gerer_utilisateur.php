<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: connexion.php');
    exit();
}

// Changer le statut d'un utilisateur
if(isset($_GET['changer_statut']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $nouveau_statut = $_GET['changer_statut'] == 'actif' ? 'actif' : 'inactif';
    
    $stmt = $pdo->prepare("UPDATE Utilisateur SET statut_compte = ? WHERE id_utilisateur = ?");
    $stmt->execute([$nouveau_statut, $id]);
    header('Location: gerer_utilisateurs.php');
    exit();
}

// Supprimer un utilisateur
if(isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM Utilisateur WHERE id_utilisateur = ? AND role != 'admin'");
    $stmt->execute([$id]);
    header('Location: gerer_utilisateurs.php');
    exit();
}

// Récupérer tous les utilisateurs (sauf l'admin courant)
$stmt = $pdo->query("
    SELECT * FROM Utilisateur 
    WHERE role != 'admin' 
    ORDER BY role, date_inscription DESC
");
$utilisateurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-users-cog"></i> Gestion des utilisateurs</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Date inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($utilisateurs as $user): ?>
                    <tr>
                        <td><?= $user['id_utilisateur'] ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['telephone']) ?></td>
                        <td>
                            <span class="badge <?= $user['role'] == 'prestataire' ? 'bg-info' : 'bg-secondary' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $user['statut_compte'] == 'actif' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $user['statut_compte'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                        <td>
                            <?php if($user['statut_compte'] == 'actif'): ?>
                                <a href="?changer_statut=inactif&id=<?= $user['id_utilisateur'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Désactiver ce compte ?')">
                                    <i class="fas fa-ban"></i> Désactiver
                                </a>
                            <?php else: ?>
                                <a href="?changer_statut=actif&id=<?= $user['id_utilisateur'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Activer ce compte ?')">
                                    <i class="fas fa-check"></i> Activer
                                </a>
                            <?php endif; ?>
                            <a href="?supprimer=<?= $user['id_utilisateur'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>