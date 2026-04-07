<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireClient();

$id_demande = intval($_GET['id_demande']);
$stmt = $pdo->prepare("SELECT d.*, s.nom_service FROM Demande d JOIN Service s ON d.id_service = s.id_service WHERE d.id_demande = ? AND d.id_client = ? AND d.statut = 'terminee'");
$stmt->execute([$id_demande, $_SESSION['user_id']]);
$demande = $stmt->fetch();

if(!$demande) {
    header('Location: mes_demandes.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    $sql = "INSERT INTO Avis (note, commentaire, id_demande) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$note, $commentaire, $id_demande])) {
        header('Location: mes_demandes.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Laisser un avis - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 10px; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 30px; color: #ddd; cursor: pointer; }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #ffc107; }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5><i class="fas fa-star"></i> Laisser un avis</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">Service : <?= htmlspecialchars($demande['nom_service']) ?></div>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Votre note</label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="note" value="5" required><label for="star5">★</label>
                                <input type="radio" id="star4" name="note" value="4"><label for="star4">★</label>
                                <input type="radio" id="star3" name="note" value="3"><label for="star3">★</label>
                                <input type="radio" id="star2" name="note" value="2"><label for="star2">★</label>
                                <input type="radio" id="star1" name="note" value="1"><label for="star1">★</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Votre commentaire</label>
                            <textarea name="commentaire" rows="4" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">Publier l'avis</button>
                        <a href="mes_demandes.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>