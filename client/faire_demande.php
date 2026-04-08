<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireClient();

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$stmt = $pdo->prepare("SELECT s.*, u.id_utilisateur as presta_id, u.nom, u.prenom FROM Service s JOIN Utilisateur u ON s.id_prestataire = u.id_utilisateur WHERE s.id_service = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if(!$service) {
    header('Location: explore.php');
    exit();
}

$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description_besoin']);
    if(!empty($description)) {
        $sql = "INSERT INTO Demande (description_besoin, id_client, id_prestataire, id_service, statut) VALUES (?, ?, ?, ?, 'en attente')";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$description, $_SESSION['user_id'], $service['presta_id'], $service_id])) {
            $message = "Demande envoyée avec succès !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Faire une demande - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .carte-demande {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        .info-service {
            background: #faf8f5;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .input-maison {
            background: #fefcf8;
            border: 1px solid #e2dcd0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
            transition: all 0.15s;
        }
        .input-maison:focus {
            border-color: #c17b4c;
            outline: none;
            background: white;
        }
        .btn-envoyer {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 12px 28px;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-envoyer:hover {
            background: #a05f38;
        }
        .btn-annuler {
            background: transparent;
            border: 1px solid #e2dcd0;
            border-radius: 40px;
            padding: 12px 28px;
            color: #8b8a86;
            transition: all 0.15s;
        }
        .btn-annuler:hover {
            border-color: #c17b4c;
            color: #c17b4c;
        }
        .alerte-succes {
            background: #e8f0ec;
            border-left: 3px solid #7c9c8e;
            padding: 12px 16px;
            border-radius: 12px;
            color: #5c7c6e;
            font-size: 0.85rem;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4 text-center">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">CLIENT</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Faire une demande</h1>
        <p style="color: #8b8a86;">Décrivez votre besoin au prestataire</p>
    </div>
    
    <div class="carte-demande">
        <div class="info-service">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span style="background: #f0ebe2; padding: 3px 10px; border-radius: 30px; font-size: 0.7rem;"><i class="fas fa-concierge-bell me-1"></i> Service</span>
                    <h5 class="mt-2 mb-1" style="font-weight: 600;"><?= htmlspecialchars($service['nom_service']) ?></h5>
                    <div class="small" style="color: #8b8a86;">
                        <i class="fas fa-user me-1"></i> Proposé par <?= htmlspecialchars($service['prenom']) ?> <?= htmlspecialchars($service['nom']) ?>
                    </div>
                </div>
                <?php if($service['prix_estime']): ?>
                    <div style="background: #fff0e6; padding: 6px 14px; border-radius: 30px; color: #c17b4c; font-weight: 600;">
                        <i class="fas fa-money-bill-wave me-1"></i> <?= number_format($service['prix_estime'], 0, ',', ' ') ?> CFA
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if($message): ?>
            <div class="alerte-succes mb-4">
                <i class="fas fa-check-circle me-2"></i> <?= $message ?>
                <div class="mt-2"><a href="mes_demandes.php" style="color: #c17b4c;">Voir mes demandes <i class="fas fa-arrow-right"></i></a></div>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="mb-4">
                    <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;"><i class="fas fa-pencil-alt me-1"></i> Décrivez votre besoin</label>
                    <textarea name="description_besoin" rows="5" class="input-maison" required placeholder="Décrivez précisément ce dont vous avez besoin, les détails importants, votre disponibilité..."></textarea>
                </div>
                
                <div class="d-flex gap-3 justify-content-end">
                    <a href="explore.php" class="btn-annuler">Annuler</a>
                    <button type="submit" class="btn-envoyer"><i class="fas fa-paper-plane me-2"></i> Envoyer la demande</button>
                </div>
            </form>
        <?php endif; ?>
        
        <div class="mt-4 pt-3" style="border-top: 1px solid #f0ebe2; font-size: 0.7rem; color: #b5a88e;">
            <i class="fas fa-info-circle me-1"></i> Le prestataire recevra votre demande et pourra l'accepter ou la refuser.
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>