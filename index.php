<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Services - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Plateforme de Services</h1>
        <p class="lead">Trouvez le prestataire idéal ou proposez vos services en toute simplicité</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="inscription.php" class="btn btn-light btn-lg mt-3">Commencez gratuitement</a>
            <a href="connexion.php" class="btn btn-outline-light btn-lg mt-3">Se connecter</a>
        <?php else: ?>
            <a href="tableau_de_bord.php" class="btn btn-light btn-lg mt-3">Accéder à mon tableau de bord</a>
        <?php endif; ?>
    </div>
</div>

<!-- Services section -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Nos services populaires</h2>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-cut fa-3x text-primary mb-3"></i>
                    <h5>Coiffure</h5>
                    <p>Coiffeurs à domicile</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-wrench fa-3x text-primary mb-3"></i>
                    <h5>Plomberie</h5>
                    <p>Dépannage et installation</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-tshirt fa-3x text-primary mb-3"></i>
                    <h5>Laverie</h5>
                    <p>Nettoyage et repassage</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-child fa-3x text-primary mb-3"></i>
                    <h5>Garde d'enfants</h5>
                    <p>Baby-sitting</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comment ça marche -->
<div class="bg-light py-5 mt-5">
    <div class="container">
        <h2 class="text-center mb-4">Comment ça marche ?</h2>
        <div class="row text-center">
            <div class="col-md-4">
                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                <h5>1. Inscription</h5>
                <p>Créez un compte gratuitement</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-search fa-3x text-primary mb-3"></i>
                <h5>2. Recherche</h5>
                <p>Trouvez le service qu'il vous faut</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                <h5>3. Mise en relation</h5>
                <p>Le prestataire vous répond</p>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <p>&copy; 2025 Plateforme de Services - Tous droits réservés</p>
        <p>
            <a href="a_propos.php" class="text-white">À propos</a> |
            <a href="#" class="text-white">Contact</a> |
            <a href="#" class="text-white">Mentions légales</a>
        </p>
    </div>
</footer>

</body>
</html>