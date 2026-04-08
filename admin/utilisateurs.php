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
    <title>Gestion utilisateurs - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .carte-admin {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 1.5rem;
        }
        .table-utilisateurs {
            width: 100%;
            border-collapse: collapse;
        }
        .table-utilisateurs th {
            text-align: left;
            padding: 12px 12px;
            background: #faf8f5;
            border-bottom: 1px solid #e2dcd0;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #8b8a86;
            font-weight: 600;
        }
        .table-utilisateurs td {
            padding: 12px 12px;
            border-bottom: 1px solid #f0ebe2;
            font-size: 0.85rem;
        }
        .table-utilisateurs tr:hover td {
            background: #fefcf9;
        }
        .badge-role {
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .badge-presta { background: #e8f0ec; color: #5c7c6e; }
        .badge-client { background: #f0ebe2; color: #8b8a86; }
        .badge-actif { background: #e8f0ec; color: #5c7c6e; }
        .badge-inactif { background: #f5e8e5; color: #b87a5a; }
        .btn-icon {
            background: transparent;
            border: none;
            padding: 6px 10px;
            border-radius: 30px;
            transition: all 0.15s;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn-activer { color: #7c9c8e; }
        .btn-activer:hover { background: #e8f0ec; }
        .btn-desactiver { color: #b87a5a; }
        .btn-desactiver:hover { background: #f5e8e5; }
        .btn-supprimer { color: #b87a5a; }
        .btn-supprimer:hover { background: #f5e8e5; }
        .compteur-badge {
            background: #f0ebe2;
            color: #8b8a86;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">ADMINISTRATION</p>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;"><i class="fas fa-users-cog me-2"></i> Utilisateurs</h1>
                <p style="color: #8b8a86;">Gérez les comptes de la plateforme</p>
            </div>
            <div class="compteur-badge">
                <i class="fas fa-users me-1"></i> <?= count($users) ?> utilisateurs
            </div>
        </div>
    </div>
    
    <div class="carte-admin">
        <div class="table-responsive">
            <table class="table-utilisateurs">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td style="color: #b5a88e;">#<?= $u['id_utilisateur'] ?></td>
                        <td><strong><?= htmlspecialchars($u['prenom']) ?> <?= htmlspecialchars($u['nom']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['telephone']) ?></td>
                        <td>
                            <span class="badge-role <?= $u['role']=='prestataire'?'badge-presta':'badge-client' ?>">
                                <?= $u['role'] == 'prestataire' ? '<i class="fas fa-tools me-1"></i> Prestataire' : '<i class="fas fa-user me-1"></i> Client' ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge-role <?= $u['statut_compte']=='actif'?'badge-actif':'badge-inactif' ?>">
                                <?= $u['statut_compte'] == 'actif' ? '<i class="fas fa-check-circle me-1"></i> Actif' : '<i class="fas fa-ban me-1"></i> Inactif' ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if($u['statut_compte'] == 'actif'): ?>
                                    <a href="?changer_statut=inactif&id=<?= $u['id_utilisateur'] ?>" class="btn-icon btn-desactiver" data-tooltip="Désactiver">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?changer_statut=actif&id=<?= $u['id_utilisateur'] ?>" class="btn-icon btn-activer" data-tooltip="Activer">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="?supprimer=<?= $u['id_utilisateur'] ?>" class="btn-icon btn-supprimer" onclick="return confirm('Supprimer définitivement cet utilisateur ?')" data-tooltip="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(count($users) == 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-users-slash fa-3x" style="color: #d4cdbe; margin-bottom: 1rem;"></i>
                <p style="color: #8b8a86;">Aucun utilisateur inscrit pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 20px; padding: 12px 16px; background: #f0ebe2; border-radius: 14px; border-left: 3px solid #c17b4c;">
        <div style="font-size: 0.7rem; color: #8b8a86;">
            <i class="fas fa-info-circle me-2" style="color: #c17b4c;"></i>
            Les utilisateurs désactivés ne peuvent pas se connecter à la plateforme.
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>