<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Plateforme Services</a>
        <div class="navbar-nav ms-auto">
            <span class="nav-link text-white">Bonjour <?= $_SESSION['user_prenom'] ?> (<?= $user_role ?>)</span>
            <a class="nav-link" href="deconnexion.php">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">
                <h4>Bienvenue sur votre tableau de bord !</h4>
                <p>Vous êtes connecté en tant que <strong><?= $user_role ?></strong>.</p>
            </div>
        </div>
    </div>

    <?php if($user_role == 'client'): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Rechercher un service</h5>
                        <p>Trouvez des prestataires près de chez vous</p>
                        <a href="#" class="btn btn-primary">Explorer</a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($user_role == 'prestataire'): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Mes services</h5>
                        <p>Gérez les services que vous proposez</p>
                        <a href="#" class="btn btn-primary">Voir</a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif($user_role == 'admin'): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Gestion utilisateurs</h5>
                        <p>Validez les prestataires et gérez les comptes</p>
                        <a href="#" class="btn btn-danger">Administrer</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>