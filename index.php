<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiLink - Plateforme de Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="hero">
    <div class="container text-center">
        <h1 class="fade-in-up">Trouvez le prestataire idéal</h1>
        <p class="lead">Ou proposez vos services en toute simplicité</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="auth/register.php" class="btn btn-light btn-lg mt-3">S'inscrire</a>
            <a href="auth/login.php" class="btn btn-outline-light btn-lg mt-3">Se connecter</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-light btn-lg mt-3">Accéder à mon tableau de bord</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-cut fa-3x text-primary mb-3"></i>
                    <h5>Coiffure</h5>
                    <p>Coiffeurs à domicile</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-wrench fa-3x text-primary mb-3"></i>
                    <h5>Plomberie</h5>
                    <p>Dépannage et installation</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-child fa-3x text-primary mb-3"></i>
                    <h5>Garde d'enfants</h5>
                    <p>Baby-sitting</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>