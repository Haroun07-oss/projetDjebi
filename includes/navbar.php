<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">
            <i class="fas fa-handshake me-2"></i>Servi<span class="text-warning">Link</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i> Accueil</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <?php if($_SESSION['user_role'] == 'client'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-search"></i> Services
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/client/explore.php">🔍 Explorer</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/client/mes_demandes.php">📋 Mes demandes</a></li>
                            </ul>
                        </li>
                    
                    <?php elseif($_SESSION['user_role'] == 'prestataire'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-tools"></i> Gestion
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/prestataire/mes_services.php">📦 Mes services</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/prestataire/ajouter_service.php">➕ Ajouter</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/prestataire/demandes_recues.php">📩 Demandes reçues</a></li>
                            </ul>
                        </li>
                    
                    <?php elseif($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-crown"></i> Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/utilisateurs.php">👥 Utilisateurs</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/categories.php">📁 Catégories</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/statistiques.php">📊 Stats</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user_prenom']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/profil.php">👤 Mon profil</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard.php">📊 Tableau de bord</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout.php">🚪 Déconnexion</a></li>
                        </ul>
                    </li>
                    
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/auth/login.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-warning btn-sm text-dark" href="<?= BASE_URL ?>/auth/register.php">Inscription</a></li>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>