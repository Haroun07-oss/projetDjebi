<nav class="navbar navbar-expand-lg" style="background: #fffefc !important; border-bottom: 1px solid #e2dcd0; padding: 0.75rem 0;">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php" style="font-weight: 700; font-size: 1.4rem; color: #2c2b28;">
            <i class="fas fa-handshake me-2" style="color: #c17b4c;"></i>
            Ivoire<span style="color: #c17b4c;">Bara</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #e2dcd0;">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php" style="color: #5c5b58; border-radius: 30px; padding: 8px 18px;">
                        <i class="fas fa-home me-1"></i> Accueil
                    </a>
                </li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <?php if($_SESSION['user_role'] == 'client'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" style="color: #5c5b58; border-radius: 30px; padding: 8px 18px;">
                                <i class="fas fa-concierge-bell me-1"></i> Services
                            </a>
                            <ul class="dropdown-menu" style="border: 1px solid #e2dcd0; border-radius: 16px; background: white; padding: 8px;">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/client/explore.php" style="border-radius: 12px; padding: 8px 16px;">🔍 Explorer</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/client/mes_demandes.php" style="border-radius: 12px; padding: 8px 16px;">📋 Mes demandes</a></li>
                            </ul>
                        </li>
                    
                    <?php elseif($_SESSION['user_role'] == 'prestataire'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" style="color: #5c5b58; border-radius: 30px; padding: 8px 18px;">
                                <i class="fas fa-tools me-1"></i> Gestion
                            </a>
                            <ul class="dropdown-menu" style="border: 1px solid #e2dcd0; border-radius: 16px; background: white; padding: 8px;">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/prestataire/mes_services.php" style="border-radius: 12px; padding: 8px 16px;">📦 Mes services</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/prestataire/ajouter_service.php" style="border-radius: 12px; padding: 8px 16px;">➕ Ajouter</a></li>
                                <li><hr class="dropdown-divider" style="margin: 6px 0;"></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/prestataire/demandes_recues.php" style="border-radius: 12px; padding: 8px 16px;">📩 Demandes reçues</a></li>
                            </ul>
                        </li>
                    
                    <?php elseif($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" style="color: #5c5b58; border-radius: 30px; padding: 8px 18px;">
                                <i class="fas fa-shield-alt me-1"></i> Admin
                            </a>
                            <ul class="dropdown-menu" style="border: 1px solid #e2dcd0; border-radius: 16px; background: white; padding: 8px;">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/utilisateurs.php" style="border-radius: 12px; padding: 8px 16px;">👥 Utilisateurs</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/categories.php" style="border-radius: 12px; padding: 8px 16px;">📁 Catégories</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/statistiques.php" style="border-radius: 12px; padding: 8px 16px;">📊 Statistiques</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" style="color: #5c5b58; border-radius: 30px; padding: 5px 12px 5px 8px;">
                            <div style="width: 32px; height: 32px; background: #f0ebe2; border-radius: 100px; display: flex; align-items: center; justify-content: center; color: #c17b4c; font-weight: 600;">
                                <?= strtoupper(substr($_SESSION['user_prenom'], 0, 1)) ?>
                            </div>
                            <span><?= htmlspecialchars($_SESSION['user_prenom']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="border: 1px solid #e2dcd0; border-radius: 16px; background: white; padding: 8px;">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/profil.php" style="border-radius: 12px; padding: 8px 16px;">👤 Mon profil</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard.php" style="border-radius: 12px; padding: 8px 16px;">📊 Tableau de bord</a></li>
                            <li><hr class="dropdown-divider" style="margin: 6px 0;"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout.php" style="border-radius: 12px; padding: 8px 16px;">🚪 Déconnexion</a></li>
                        </ul>
                    </li>
                    
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/auth/login.php" style="background: transparent; border: 1px solid #c17b4c; border-radius: 40px; padding: 8px 22px; color: #c17b4c;">
                            Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/auth/register.php" style="background: #c17b4c; border-radius: 40px; padding: 8px 22px; color: white;">
                            Inscription
                        </a>
                    </li>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>