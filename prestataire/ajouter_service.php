<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requirePrestataire();

$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_service']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix_estime']);
    $id_categorie = intval($_POST['id_categorie']);
    
    $sql = "INSERT INTO Service (nom_service, description_service, prix_estime, id_prestataire, id_categorie) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$nom, $description, $prix, $_SESSION['user_id'], $id_categorie])) {
        $message = "Service ajouté avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un service - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .carte-form {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 2rem;
            max-width: 700px;
            margin: 0 auto;
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
        .btn-ajout {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 12px 24px;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-ajout:hover {
            background: #a05f38;
            transform: translateY(-1px);
        }
        .btn-annuler {
            background: transparent;
            border: 1px solid #e2dcd0;
            border-radius: 40px;
            padding: 12px 24px;
            color: #8b8a86;
            transition: all 0.15s;
        }
        .btn-annuler:hover {
            border-color: #c17b4c;
            color: #c17b4c;
            background: #fefcf8;
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
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">PRESTATAIRE</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Ajouter un service</h1>
        <p style="color: #8b8a86;">Proposez votre talent à la communauté</p>
    </div>
    
    <div class="carte-form">
        <?php if($message): ?>
            <div class="alerte-succes mb-4">
                <i class="fas fa-check-circle me-2"></i> <?= $message ?>
                <div class="mt-2"><a href="mes_services.php" style="color: #c17b4c;">Voir mes services →</a></div>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Nom du service</label>
                <input type="text" name="nom_service" class="input-maison" required placeholder="Ex: Cours de piano, Plomberie, Coiffure...">
            </div>
            
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Description</label>
                <textarea name="description" rows="4" class="input-maison" required placeholder="Décrivez votre service en détail..."></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Prix estimé (CFA)</label>
                    <input type="number" step="100" name="prix_estime" class="input-maison" placeholder="Ex: 5000">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Catégorie</label>
                    <select name="id_categorie" class="input-maison" required>
                        <option value="">Choisir une catégorie</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id_categorie'] ?>"><?= htmlspecialchars($c['nom_categorie']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn-ajout">📌 Publier le service</button>
                <a href="mes_services.php" class="btn-annuler">Annuler</a>
            </div>
        </form>
        
        <div class="mt-4 pt-3" style="border-top: 1px solid #f0ebe2; font-size: 0.7rem; color: #b5a88e;">
            <i class="fas fa-info-circle me-1"></i> Votre service sera visible par tous les clients après validation.
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>