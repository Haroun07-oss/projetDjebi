<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'client') {
    header('Location: connexion.php');
    exit();
}

$id_demande = isset($_GET['id_demande']) ? intval($_GET['id_demande']) : 0;

// Vérifier que la demande appartient au client et est terminée
$stmt = $pdo->prepare("
    SELECT d.*, s.nom_service, u.nom as presta_nom, u.prenom as presta_prenom,
           (SELECT id_avis FROM Avis WHERE id_demande = d.id_demande) as avis_existe
    FROM Demande d
    JOIN Service s ON d.id_service = s.id_service
    JOIN Utilisateur u ON d.id_prestataire = u.id_utilisateur
    WHERE d.id_demande = ? AND d.id_client = ? AND d.statut = 'terminee'
");
$stmt->execute([$id_demande, $_SESSION['user_id']]);
$demande = $stmt->fetch();

if(!$demande) {
    header('Location: mes_demandes.php');
    exit();
}

if($demande['avis_existe']) {
    header('Location: mes_demandes.php');
    exit();
}

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    
    if($note < 1 || $note > 5) {
        $erreur = "La note doit être comprise entre 1 et 5.";
    } elseif(empty($commentaire)) {
        $erreur = "Veuillez laisser un commentaire.";
    } else {
        $sql = "INSERT INTO Avis (note, commentaire, id_demande) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$note, $commentaire, $id_demande])) {
            $message = "Merci pour votre avis !";
        } else {
            $erreur = "Erreur lors de l'envoi de l'avis.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Laisser un avis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 10px;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
            transition: 0.2s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffc107;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4><i class="fas fa-star"></i> Laisser un avis</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Service :</strong> <?= htmlspecialchars($demande['nom_service']) ?><br>
                        <strong>Prestataire :</strong> <?= htmlspecialchars($demande['presta_prenom']) ?> <?= htmlspecialchars($demande['presta_nom']) ?>
                    </div>

                    <?php if($message): ?>
                        <div class="alert alert-success">
                            <?= $message ?>
                            <br><a href="mes_demandes.php" class="btn btn-success mt-2">Retour à mes demandes</a>
                        </div>
                    <?php else: ?>
                        <?php if($erreur): ?>
                            <div class="alert alert-danger"><?= $erreur ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Votre note *</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="note" value="5" required>
                                    <label for="star5">★</label>
                                    <input type="radio" id="star4" name="note" value="4">
                                    <label for="star4">★</label>
                                    <input type="radio" id="star3" name="note" value="3">
                                    <label for="star3">★</label>
                                    <input type="radio" id="star2" name="note" value="2">
                                    <label for="star2">★</label>
                                    <input type="radio" id="star1" name="note" value="1">
                                    <label for="star1">★</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Votre commentaire *</label>
                                <textarea name="commentaire" rows="5" class="form-control" required placeholder="Partagez votre expérience avec ce prestataire..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-warning">Publier l'avis</button>
                            <a href="mes_demandes.php" class="btn btn-secondary">Annuler</a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>