<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'prestataire') {
    header('Location: connexion.php');
    exit();
}

$message = '';
$erreur = '';

// Récupérer les catégories pour le select
$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_service = trim($_POST['nom_service']);
    $description = trim($_POST['description']);
    $prix_estime = floatval($_POST['prix_estime']);
    $id_categorie = intval($_POST['id_categorie']);
    $id_prestataire = $_SESSION['user_id'];

    if(empty($nom_service) || empty($description)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $sql = "INSERT INTO Service (nom_service, description_service, prix_estime, id_prestataire, id_categorie) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if($stmt->execute([$nom_service, $description, $prix_estime, $id_prestataire, $id_categorie])) {
            $message = "Votre service a bien été ajouté !";
        } else {
            $erreur = "Erreur lors de l'ajout.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Proposer un service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4>Proposer un nouveau service</h4>
                </div>
                <div class="card-body">
                    <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>
                    <?php if($erreur): ?>
                        <div class="alert alert-danger"><?= $erreur ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Nom du service *</label>
                            <input type="text" name="nom_service" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Description détaillée *</label>
                            <textarea name="description" rows="4" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Prix estimé (€)</label>
                            <input type="number" step="0.01" name="prix_estime" class="form-control">
                            <small class="text-muted">Optionnel - indiquez un tarif indicatif</small>
                        </div>

                        <div class="mb-3">
                            <label>Catégorie *</label>
                            <select name="id_categorie" class="form-select" required>
                                <option value="">Choisir une catégorie</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Publier le service</button>
                        <a href="tableau_de_bord.php" class="btn btn-secondary">Annuler</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>