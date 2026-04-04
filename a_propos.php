<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>À propos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h3><i class="fas fa-info-circle"></i> À propos de Plateforme Services</h3>
                </div>
                <div class="card-body">
                    <h5>Notre mission</h5>
                    <p>Mettre en relation des clients ayant besoin d'un service et des prestataires qualifiés dans différents domaines.</p>
                    
                    <h5 class="mt-4">Services proposés</h5>
                    <ul>
                        <li>Coiffure</li>
                        <li>Plomberie</li>
                        <li>Laverie</li>
                        <li>Garde d'enfants</li>
                    </ul>
                    
                    <h5 class="mt-4">Comment ça fonctionne ?</h5>
                    <ol>
                        <li><strong>Inscrivez-vous</strong> gratuitement (client ou prestataire)</li>
                        <li><strong>Les clients</strong> : explorez les services et faites une demande</li>
                        <li><strong>Les prestataires</strong> : proposez vos services et gérez vos demandes</li>
                        <li><strong>Notez</strong> la prestation après réalisation</li>
                    </ol>
                    
                    <div class="alert alert-info mt-4 text-center">
                        <i class="fas fa-envelope"></i> Contact : contact@plateforme-services.com<br>
                        <i class="fas fa-phone"></i> Tél : +225 01 23 45 67
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>