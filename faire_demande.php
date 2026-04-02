<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header('Location: connexion.php');
    exit();
}

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
if(!$service_id) {
    header('Location: explorer_services.php');
    exit();
}

// Récupérer les infos du service
$stmt = $pdo->prepare("
    SELECT s.*, u.id_utilisateur as prestataire_id, u.nom, u.prenom 
    FROM Service s
    JOIN Utilisateur u ON s.id_prestataire = u.id_utilisateur
    WHERE s.id_service = ?
");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if(!$service) {
    header('Location: explorer_services.php');
    exit();
}

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description_besoin = trim($_POST['description_besoin']);
    $id_client = $_SESSION['user_id'];
    $id_prestataire = $service['prestataire_id'];

    if(empty($description_besoin)) {
        $erreur = "Veuillez décrire votre besoin.";
    } else {
        $sql = "INSERT INTO Demande (description_besoin, id_client, id_prestataire, id_service, statut) 
                VALUES (?, ?, ?, ?, 'en attente')";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$description_besoin, $id_client, $id_prestataire, $service_id])) {
            $message = "Votre demande a été envoyée au prestataire !";
        } else {
            $erreur = "Erreur lors de l'envoi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Faire une demande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Demande de service</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Service :</strong> <?= htmlspecialchars($service['nom_service']) ?><br>
                        <strong>Prestataire :</strong> <?= htmlspecialchars($service['prenom']) ?> <?= htmlspecialchars($service['nom']) ?>
                    </div>

                    <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>
                    <?php if($erreur): ?>
                        <div class="alert alert-danger"><?= $erreur ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Décrivez votre besoin *</label>
                            <textarea name="description_besoin" rows="5" class="form-control" required placeholder="Ex: J'ai besoin d'une coupe homme à domicile, le mardi après-midi..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer la demande</button>
                        <a href="explorer_services.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>