<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IvoireBara - Mettre en relation les talents ivoiriens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-accueil {
            background: #f5efe7;
            padding: 70px 0;
            border-bottom: 1px solid #e2dcd0;
        }
        .carte-service {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.8rem 1.2rem;
            text-align: center;
            transition: all 0.2s;
            height: 100%;
        }
        .carte-service:hover {
            border-color: #c17b4c;
            transform: translateY(-3px);
        }
        .carte-service i {
            font-size: 2rem;
            color: #c17b4c;
            margin-bottom: 1rem;
        }
        .carte-service h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .carte-service p {
            font-size: 0.8rem;
            color: #8b8a86;
            margin: 0;
        }
        .bloc-chiffres {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 2rem;
        }
        .btn-accueil {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 10px 28px;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-accueil:hover {
            background: #a05f38;
            transform: translateY(-1px);
        }
        .btn-outline-accueil {
            background: transparent;
            border: 1px solid #c17b4c;
            border-radius: 40px;
            padding: 10px 28px;
            color: #c17b4c;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-outline-accueil:hover {
            background: #f0ebe2;
            border-color: #a05f38;
            color: #a05f38;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include 'includes/navbar.php'; ?>

<main>
    <div class="hero-accueil">
        <div class="container text-center">
            <div style="max-width: 700px; margin: 0 auto;">
                <p style="font-size: 0.75rem; color: #c17b4c; letter-spacing: 2px; margin-bottom: 1rem;">PLATEFORME IVOIRIENNE</p>
                <h1 style="font-size: 2.8rem; font-weight: 600; letter-spacing: -1px; color: #2c2b28;">Le bon prestataire<br>est ici</h1>
                <p style="color: #8b8a86; margin-top: 1rem; font-size: 1.1rem;">Que vous cherchiez un service ou que vous vouliez proposer le vôtre, IvoireBara connecte les talents de Côte d'Ivoire.</p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                        <a href="auth/register.php" class="btn-accueil">👋 Je m'inscris</a>
                        <a href="auth/login.php" class="btn-outline-accueil">🔐 Déjà inscrit</a>
                    </div>
                <?php else: ?>
                    <a href="dashboard.php" class="btn-accueil mt-3 d-inline-block">📋 Accéder à mon tableau</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="text-center mb-5">
            <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">CE QUE NOUS PROPOSONS</p>
            <h2 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Des services pour tous les jours</h2>
            <p style="color: #8b8a86;">Des milliers de prestataires vérifiés près de chez vous</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="carte-service">
                    <i class="fas fa-cut"></i>
                    <h5>Coiffure & Beauté</h5>
                    <p>Coiffeurs à domicile, barbiers, manucure...</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="carte-service">
                    <i class="fas fa-wrench"></i>
                    <h5>Bricolage & Réparations</h5>
                    <p>Plomberie, électricité, menuiserie...</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="carte-service">
                    <i class="fas fa-laptop-code"></i>
                    <h5>Informatique</h5>
                    <p>Dépannage PC, création de sites...</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="carte-service">
                    <i class="fas fa-chalkboard-user"></i>
                    <h5>Cours particuliers</h5>
                    <p>Toutes matières, tous niveaux</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="carte-service">
                    <i class="fas fa-truck-fast"></i>
                    <h5>Livraison</h5>
                    <p>Courses, colis, repas...</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="carte-service">
                    <i class="fas fa-hand-sparkles"></i>
                    <h5>Ménage & Nettoyage</h5>
                    <p>Femme de ménage, nettoyage...</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4 mb-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <div class="bloc-chiffres text-center">
                    <i class="fas fa-users fa-2x" style="color: #c17b4c;"></i>
                    <h3 style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;">+2 500</h3>
                    <p style="color: #8b8a86;">prestataires inscrits</p>
                    <hr style="width: 50px; margin: 1rem auto; border-color: #e2dcd0;">
                    <i class="fas fa-check-circle fa-2x" style="color: #7c9c8e;"></i>
                    <h3 style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;">98%</h3>
                    <p style="color: #8b8a86;">de clients satisfaits</p>
                </div>
            </div>
            <div class="col-md-6">
                <h2 style="font-size: 1.6rem; font-weight: 600;">Pourquoi nous choisir ?</h2>
                <div class="mt-4">
                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-shield-alt fa-lg" style="color: #c17b4c;"></i>
                        <div>
                            <strong>Prestataires vérifiés</strong>
                            <p class="text-muted small mb-0">Chaque profil est examiné avant validation</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-comments fa-lg" style="color: #c17b4c;"></i>
                        <div>
                            <strong>Messagerie intégrée</strong>
                            <p class="text-muted small mb-0">Discutez directement sans passer par l'externe</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <i class="fas fa-star fa-lg" style="color: #c17b4c;"></i>
                        <div>
                            <strong>Système d'avis</strong>
                            <p class="text-muted small mb-0">Notez vos prestataires et aidez la communauté</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>