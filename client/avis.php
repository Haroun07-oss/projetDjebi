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
    <title>Laisser un avis - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .carte-avis {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 2rem;
            max-width: 650px;
            margin: 0 auto;
        }
        .info-service-avis {
            background: #faf8f5;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
            margin: 10px 0;
        }
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 32px;
            color: #e2dcd0;
            cursor: pointer;
            transition: all 0.15s;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #d4a02b;
        }
        .input-avis {
            background: #fefcf8;
            border: 1px solid #e2dcd0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
            transition: all 0.15s;
        }
        .input-avis:focus {
            border-color: #c17b4c;
            outline: none;
            background: white;
        }
        .btn-publier {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 12px 28px;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-publier:hover {
            background: #a05f38;
        }
        .btn-skipper {
            background: transparent;
            border: 1px solid #e2dcd0;
            border-radius: 40px;
            padding: 12px 28px;
            color: #8b8a86;
            transition: all 0.15s;
        }
        .btn-skipper:hover {
            border-color: #c17b4c;
            color: #c17b4c;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4 text-center">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">CLIENT</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">⭐ Laisser un avis</h1>
        <p style="color: #8b8a86;">Partagez votre expérience avec la communauté</p>
    </div>
    
    <div class="carte-avis">
        <div class="info-service-avis">
            <div class="text-center">
                <span style="background: #f0ebe2; padding: 3px 10px; border-radius: 30px; font-size: 0.7rem;">Service terminé</span>
                <h5 class="mt-2 mb-0" style="font-weight: 600;"><?= htmlspecialchars($demande['nom_service']) ?></h5>
            </div>
        </div>
        
        <form method="POST">
            <div class="mb-4 text-center">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 10px; display: block;">Votre note</label>
                <div class="star-rating justify-content-center">
                    <input type="radio" id="star5" name="note" value="5" required><label for="star5">★</label>
                    <input type="radio" id="star4" name="note" value="4"><label for="star4">★</label>
                    <input type="radio" id="star3" name="note" value="3"><label for="star3">★</label>
                    <input type="radio" id="star2" name="note" value="2"><label for="star2">★</label>
                    <input type="radio" id="star1" name="note" value="1"><label for="star1">★</label>
                </div>
            </div>
            
            <div class="mb-4">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">💬 Votre commentaire</label>
                <textarea name="commentaire" rows="4" class="input-avis" required placeholder="Qu'avez-vous pensé du service ? Le prestataire était-il professionnel ? ..."></textarea>
            </div>
            
            <div class="d-flex gap-3 justify-content-end">
                <a href="mes_demandes.php" class="btn-skipper">Plus tard</a>
                <button type="submit" class="btn-publier">📝 Publier l'avis</button>
            </div>
        </form>
        
        <div class="mt-4 pt-3" style="border-top: 1px solid #f0ebe2; font-size: 0.7rem; color: #b5a88e; text-align: center;">
            <i class="fas fa-heart me-1" style="color: #c17b4c;"></i> Votre avis aide d'autres clients à faire leur choix
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>