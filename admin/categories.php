<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireAdmin();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom_categorie']);
    $desc = trim($_POST['description']);
    if(!empty($nom)) {
        $pdo->prepare("INSERT INTO Categorie (nom_categorie, description) VALUES (?, ?)")->execute([$nom, $desc]);
        header('Location: categories.php');
        exit();
    }
}

if(isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM Categorie WHERE id_categorie = ?")->execute([$_GET['supprimer']]);
    header('Location: categories.php');
    exit();
}

$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion catégories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-plus"></i> Ajouter une catégorie</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nom</label>
                            <input type="text" name="nom_categorie" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" name="ajouter" class="btn btn-primary w-100">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5><i class="fas fa-list"></i> Catégories existantes</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach($categories as $c): ?>
                            <div class="list-group-item d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($c['nom_categorie']) ?></strong>
                                    <?php if($c['description']): ?><br><small><?= htmlspecialchars($c['description']) ?></small><?php endif; ?>
                                </div>
                                <a href="?supprimer=<?= $c['id_categorie'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>