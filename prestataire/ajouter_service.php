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
    <title>Ajouter un service - ServiLink</title>
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
                <div class="card-header bg-success text-white">
                    <h5><i class="fas fa-plus"></i> Nouveau service</h5>
                </div>
                <div class="card-body">
                    <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?> <a href="mes_services.php">Voir mes services</a></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nom du service</label>
                            <input type="text" name="nom_service" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" rows="4" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Prix estimé (€)</label>
                            <input type="number" step="0.01" name="prix_estime" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Catégorie</label>
                            <select name="id_categorie" class="form-select" required>
                                <option value="">Choisir</option>
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_categorie'] ?>"><?= htmlspecialchars($c['nom_categorie']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Publier</button>
                        <a href="mes_services.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>