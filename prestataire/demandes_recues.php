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
    <title>Demandes reçues - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .demande-recue {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
            margin-bottom: 1.2rem;
            transition: all 0.2s;
        }
        .demande-recue:hover {
            border-color: #c17b4c;
        }
        .statut-badge {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 500;
        }
        .statut-attente { background: #f0ebe2; color: #b5a88e; }
        .statut-acceptee { background: #e8f0ec; color: #7c9c8e; }
        .statut-terminee { background: #e8f0ec; color: #7c9c8e; }
        .statut-refusee { background: #f5e8e5; color: #b87a5a; }
        .besoin-box {
            background: #faf8f5;
            border-radius: 14px;
            padding: 12px;
            margin: 12px 0;
        }
        select.form-select-perso {
            background: #fefcf8;
            border: 1px solid #e2dcd0;
            border-radius: 30px;
            padding: 8px 16px;
            font-size: 0.8rem;
        }
        .btn-valider {
            background: #c17b4c;
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            color: white;
            font-size: 0.8rem;
            transition: all 0.15s;
        }
        .btn-valider:hover {
            background: #a05f38;
        }
        .btn-terminer {
            background: #7c9c8e;
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            color: white;
            font-size: 0.8rem;
            transition: all 0.15s;
        }
        .btn-terminer:hover {
            background: #5c7c6e;
        }
        .info-client {
            font-size: 0.75rem;
            color: #8b8a86;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #f0ebe2;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">PRESTATAIRE</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">📩 Demandes reçues</h1>
        <p style="color: #8b8a86;">Répondez aux demandes de vos clients</p>
    </div>
    
    <?php if(count($demandes) == 0): ?>
        <div class="text-center py-5" style="background: #fffef7; border: 1px solid #e2dcd0; border-radius: 20px;">
            <i class="fas fa-envelope-open-text fa-3x" style="color: #d4cdbe; margin-bottom: 1rem;"></i>
            <p style="color: #8b8a86;">Aucune demande reçue pour le moment.</p>
            <a href="mes_services.php" style="color: #c17b4c; text-decoration: none;">Voir mes services</a>
        </div>
    <?php else: ?>
        <?php foreach($demandes as $d): ?>
            <div class="demande-recue">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="mb-1" style="font-weight: 600;"><?= htmlspecialchars($d['nom_service']) ?></h5>
                        <div class="small" style="color: #8b8a86;">
                            <i class="fas fa-user"></i> Client : <?= htmlspecialchars($d['prenom']) ?> <?= htmlspecialchars($d['nom']) ?>
                        </div>
                    </div>
                    <span class="statut-badge <?= $d['statut']=='en attente'?'statut-attente':($d['statut']=='acceptee'?'statut-acceptee':($d['statut']=='terminee'?'statut-terminee':'statut-refusee')) ?>">
                        <?= $d['statut'] == 'en attente' ? '⏳ En attente' : ($d['statut'] == 'acceptee' ? '✅ Acceptée' : ($d['statut'] == 'terminee' ? '✨ Terminée' : '❌ Refusée')) ?>
                    </span>
                </div>
                
                <div class="besoin-box">
                    <div class="small" style="color: #8b8a86; margin-bottom: 5px;">📋 Besoin du client :</div>
                    <p class="mb-0" style="font-size: 0.85rem;"><?= nl2br(htmlspecialchars($d['description_besoin'])) ?></p>
                </div>
                
                <div class="info-client">
                    <i class="fas fa-phone"></i> <?= htmlspecialchars($d['telephone']) ?> &nbsp;|&nbsp;
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($d['email']) ?>
                </div>
                
                <?php if($d['statut'] == 'en attente'): ?>
                    <form method="POST" class="mt-3 d-flex gap-2 flex-wrap">
                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                        <select name="nouveau_statut" class="form-select-perso">
                            <option value="acceptee">✅ Accepter la demande</option>
                            <option value="refusee">❌ Refuser</option>
                        </select>
                        <button type="submit" name="changer_statut" class="btn-valider">
                            Valider
                        </button>
                    </form>
                <?php elseif($d['statut'] == 'acceptee'): ?>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="id_demande" value="<?= $d['id_demande'] ?>">
                        <input type="hidden" name="nouveau_statut" value="terminee">
                        <button type="submit" name="changer_statut" class="btn-terminer">
                            ✔️ Marquer comme terminée
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>