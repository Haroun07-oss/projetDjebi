<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: connexion.php');
    exit();
}

// Ajouter une catégorie
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom_categorie']);
    $description = trim($_POST['description']);
    
    if(!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO Categorie (nom_categorie, description) VALUES (?, ?)");
        $stmt->execute([$nom, $description]);
        header('Location: gerer_categories.php');
        exit();
    }
}

// Supprimer une catégorie
if(isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $pdo->prepare("DELETE FROM Categorie WHERE id_categorie = ?")->execute([$id]);
    header('Location: gerer_categories.php');
    exit();
}

$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des catégories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

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
                            <label>Nom de la catégorie *</label>
                            <input type="text" name="nom_categorie" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" rows="3" class="form-control"></textarea>
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
                        <?php foreach($categories as $cat): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($cat['nom_categorie']) ?></strong>
                                    <?php if($cat['description']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($cat['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <a href="?supprimer=<?= $cat['id_categorie'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette catégorie ? Cela supprimera aussi les services associés.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>