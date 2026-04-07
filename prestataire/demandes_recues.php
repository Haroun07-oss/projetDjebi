<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requirePrestataire();

if(isset($_POST['changer_statut'])) {
    $id_demande = intval($_POST['id_demande']);
    $statut = $_POST['nouveau_statut'];
    $pdo->prepare("UPDATE Demande SET statut = ? WHERE id_demande = ? AND id_prestataire = ?")->execute([$statut, $id_demande, $_SESSION['user_id']]);
    header('Location: demandes_recues.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT d.*, u.nom, u.prenom, u.telephone, u.email, s.nom_service
    FROM Demande d
    JOIN Utilisateur u ON d.id_client = u.id_utilisateur
    JOIN Service s ON d.id_service = s.id_service
    WHERE d.id_prestataire = ?
    ORDER BY FIELD(d.statut, 'en attente', 'acceptee', 'terminee', 'refusee'), d.date_demande DESC
");
$stmt->execute([$_SESSION['user_id']]);
$demandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes reçues - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-inbox"></i> Demandes reçues</h2>
    
    <?php if(count($demandes) == 0): ?>
        <div class="alert alert-info">Aucune demande reçue pour le moment.</div>
    <?php else: ?>
        <?php foreach($demandes as $d): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <strong><?= htmlspecialchars($d['prenom']) ?> <?= htmlspecialchars($d['nom']) ?></strong>
                    <span class="badge bg-<?= $d['statut']=='en attente'?'warning':($d['statut']=='acceptee'?'info':($d['statut']=='terminee'?'success':'danger')) ?>">
                        <?= $d['statut'] ?>
                    </span>
                </div>
                <div class="card-body">
                    <h5><?= htmlspecialchars($d['nom_service']) ?></h5>
                    <p><strong>Besoin :</strong> <?= nl2br(htmlspecialchars($d['description_besoin'])) ?></p>
                    <p class="text-muted">Reçue le <?= date('d/m/Y H:i', strtotime($d['date_demande'])) ?></p>
                    
                    <?php if($d['statut'] == 'en attente'): ?>
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                            <select name="nouveau_statut" class="form-select d-inline w-auto">
                                <option value="acceptee">✅ Accepter</option>
                                <option value="refusee">❌ Refuser</option>
                            </select>
                            <button type="submit" name="changer_statut" class="btn btn-primary btn-sm">Valider</button>
                        </form>
                    <?php elseif($d['statut'] == 'acceptee'): ?>
                        <form method="POST" class="mt-2">
                            <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                            <input type="hidden" name="nouveau_statut" value="terminee">
                            <button type="submit" name="changer_statut" class="btn btn-success btn-sm">✔️ Marquer terminée</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>