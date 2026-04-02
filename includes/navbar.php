<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="tableau_de_bord.php">🏠 Plateforme Services</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <?php if($_SESSION['user_role'] == 'client'): ?>
                        <li class="nav-item"><a class="nav-link" href="explorer_services.php">🔍 Explorer</a></li>
                        <li class="nav-item"><a class="nav-link" href="mes_demandes.php">📋 Mes demandes</a></li>
                    
                    <?php elseif($_SESSION['user_role'] == 'prestataire'): ?>
                        <li class="nav-item"><a class="nav-link" href="mes_services.php">🛠️ Mes services</a></li>
                        <li class="nav-item"><a class="nav-link" href="proposer_service.php">➕ Ajouter</a></li>
                        <li class="nav-item"><a class="nav-link" href="demandes_recues.php">📩 Demandes reçues</a></li>
                    
                    <?php elseif($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="gerer_utilisateurs.php">👥 Utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link" href="gerer_categories.php">📁 Catégories</a></li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            👤 <?= htmlspecialchars($_SESSION['user_prenom']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil.php">Mon profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="deconnexion.php">🚪 Déconnexion</a></li>
                        </ul>
                    </li>
                    
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="connexion.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link" href="inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>