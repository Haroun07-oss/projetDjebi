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
    <title>Faire une demande - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-paper-plane"></i> Demande de service
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Service :</strong> <?= htmlspecialchars($service['nom_service']) ?><br>
                        <strong>Prestataire :</strong> <?= htmlspecialchars($service['prenom']) ?> <?= htmlspecialchars($service['nom']) ?>
                    </div>
                    <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?> <a href="mes_demandes.php">Voir mes demandes</a></div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Décrivez votre besoin</label>
                                <textarea name="description_besoin" rows="5" class="form-control" required placeholder="Décrivez précisément ce dont vous avez besoin..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer la demande</button>
                            <a href="explore.php" class="btn btn-secondary">Annuler</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>