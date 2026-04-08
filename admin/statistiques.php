<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireAdmin();

// Récupération des données
$total_users = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role != 'admin'")->fetchColumn();
$total_prestataires = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role = 'prestataire'")->fetchColumn();
$total_clients = $pdo->query("SELECT COUNT(*) FROM Utilisateur WHERE role = 'client'")->fetchColumn();
$total_services = $pdo->query("SELECT COUNT(*) FROM Service")->fetchColumn();
$total_demandes = $pdo->query("SELECT COUNT(*) FROM Demande")->fetchColumn();

// Stats par statut
$demandes_attente = $pdo->query("SELECT COUNT(*) FROM Demande WHERE statut = 'en attente'")->fetchColumn();
$demandes_acceptees = $pdo->query("SELECT COUNT(*) FROM Demande WHERE statut = 'acceptee'")->fetchColumn();
$demandes_terminees = $pdo->query("SELECT COUNT(*) FROM Demande WHERE statut = 'terminee'")->fetchColumn();
$demandes_refusees = $pdo->query("SELECT COUNT(*) FROM Demande WHERE statut = 'refusee'")->fetchColumn();

$moyenne_notes = $pdo->query("SELECT AVG(note) FROM Avis")->fetchColumn();

// Top 5 catégories les plus utilisées
$top_categories = $pdo->query("
    SELECT c.nom_categorie, COUNT(s.id_service) as total
    FROM Categorie c
    LEFT JOIN Service s ON c.id_categorie = s.id_categorie
    GROUP BY c.id_categorie
    ORDER BY total DESC
    LIMIT 5
")->fetchAll();

// Derniers inscrits
$derniers_users = $pdo->query("
    SELECT nom, prenom, role, date_inscription 
    FROM Utilisateur 
    WHERE role != 'admin' 
    ORDER BY date_inscription DESC 
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stats - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Tout fait main, pas de graphiques js complexes */
        .stat-box {
            background: white;
            border: 1px solid #e2dcd0;
            border-radius: 20px;
            padding: 1.2rem;
            transition: all 0.2s;
        }
        
        .stat-box:hover {
            border-color: #c17b4c;
            background: #fffef9;
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c2b28;
            letter-spacing: -1px;
            line-height: 1.1;
        }
        
        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #b5a88e;
            margin-top: 8px;
        }
        
        .stat-icon {
            font-size: 1.8rem;
            color: #c17b4c;
            opacity: 0.6;
        }
        
        .carte-stats {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 1.5rem;
        }
        
        .titre-carte {
            font-weight: 600;
            font-size: 1rem;
            border-left: 3px solid #c17b4c;
            padding-left: 12px;
            margin-bottom: 1.2rem;
        }
        
        /* Barres de progression faites main */
        .progress-bar-manual {
            background: #f0ebe2;
            border-radius: 20px;
            height: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            background: #c17b4c;
            height: 100%;
            border-radius: 20px;
            width: 0%;
        }
        
        .statut-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0ebe2;
        }
        
        .statut-item:last-child {
            border-bottom: none;
        }
        
        .statut-nom {
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .statut-valeur {
            font-weight: 600;
            color: #c17b4c;
        }
        
        .user-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0ebe2;
            font-size: 0.85rem;
        }
        
        .user-row:last-child {
            border-bottom: none;
        }
        
        .note-etoiles {
            color: #d4a02b;
            font-size: 1rem;
            letter-spacing: 2px;
        }
        
        .badge-stats {
            background: #f0ebe2;
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            color: #8b8a86;
        }
        
        hr.doux {
            border: none;
            border-top: 1px solid #e8e0d4;
            margin: 1rem 0;
        }
        
        @media (max-width: 768px) {
            .stat-number {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    
    <!-- Entête -->
    <div class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-chart-simple" style="color: #c17b4c; font-size: 1.3rem;"></i>
            <span style="font-size: 0.75rem; color: #b5a88e; letter-spacing: 1px;">PANORAMA</span>
        </div>
        <h1 style="font-size: 2rem; font-weight: 600; letter-spacing: -0.5px; color: #2c2b28;">Statistiques</h1>
        <p style="color: #8b8a86; margin-top: 8px;">Un coup d'œil sur l'activité de la plateforme</p>
    </div>
    
    <!-- Cartes principales - 4 grands chiffres -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-number"><?= $total_users ?></div>
                    <i class="fas fa-users stat-icon"></i>
                </div>
                <div class="stat-label">Utilisateurs</div>
                <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                    dont <?= $total_prestataires ?> prestataires
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-number"><?= $total_services ?></div>
                    <i class="fas fa-concierge-bell stat-icon"></i>
                </div>
                <div class="stat-label">Services</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-number"><?= $total_demandes ?></div>
                    <i class="fas fa-file-signature stat-icon"></i>
                </div>
                <div class="stat-label">Demandes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-number"><?= number_format($moyenne_notes ?: 0, 1) ?></div>
                    <i class="fas fa-star stat-icon"></i>
                </div>
                <div class="stat-label">Note moyenne</div>
                <div class="note-etoiles mt-1" style="font-size: 0.7rem;">
                    <?php 
                    $note_moy = round($moyenne_notes ?: 0);
                    for($i=1; $i<=5; $i++): 
                        echo $i <= $note_moy ? '★' : '☆';
                    endfor; 
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        
        <!-- Répartition des statuts de demandes -->
        <div class="col-md-6">
            <div class="carte-stats">
                <div class="titre-carte">
                    <i class="fas fa-chart-pie me-2" style="color: #c17b4c;"></i> 
                    Demandes par statut
                </div>
                
                <?php 
                $total_demandes_all = $demandes_attente + $demandes_acceptees + $demandes_terminees + $demandes_refusees;
                if($total_demandes_all > 0):
                ?>
                    <div class="statut-item">
                        <span class="statut-nom">⏳ En attente</span>
                        <span class="statut-valeur"><?= $demandes_attente ?></span>
                    </div>
                    <div class="progress-bar-manual mb-3">
                        <div class="progress-fill" style="width: <?= round(($demandes_attente / $total_demandes_all) * 100) ?>%;"></div>
                    </div>
                    
                    <div class="statut-item">
                        <span class="statut-nom">✅ Acceptées</span>
                        <span class="statut-valeur"><?= $demandes_acceptees ?></span>
                    </div>
                    <div class="progress-bar-manual mb-3">
                        <div class="progress-fill" style="width: <?= round(($demandes_acceptees / $total_demandes_all) * 100) ?>%; background: #7c9c8e;"></div>
                    </div>
                    
                    <div class="statut-item">
                        <span class="statut-nom">✨ Terminées</span>
                        <span class="statut-valeur"><?= $demandes_terminees ?></span>
                    </div>
                    <div class="progress-bar-manual mb-3">
                        <div class="progress-fill" style="width: <?= round(($demandes_terminees / $total_demandes_all) * 100) ?>%; background: #7c9c8e;"></div>
                    </div>
                    
                    <div class="statut-item">
                        <span class="statut-nom">❌ Refusées</span>
                        <span class="statut-valeur"><?= $demandes_refusees ?></span>
                    </div>
                    <div class="progress-bar-manual">
                        <div class="progress-fill" style="width: <?= round(($demandes_refusees / $total_demandes_all) * 100) ?>%; background: #b87a5a;"></div>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4" style="font-size: 0.85rem;">Aucune demande pour le moment</p>
                <?php endif; ?>
                
                <hr class="doux">
                <div class="small text-muted" style="font-size: 0.7rem;">
                    <i class="fas fa-chart-line me-1"></i> Total : <?= $total_demandes_all ?> demandes
                </div>
            </div>
        </div>
        
        <!-- Top catégories -->
        <div class="col-md-6">
            <div class="carte-stats">
                <div class="titre-carte">
                    <i class="fas fa-fire me-2" style="color: #c17b4c;"></i> 
                    Catégories populaires
                </div>
                
                <?php if(count($top_categories) > 0): ?>
                    <?php 
                    $max_total = max(array_column($top_categories, 'total'));
                    if($max_total == 0) $max_total = 1;
                    ?>
                    <?php foreach($top_categories as $cat): ?>
                        <div class="statut-item">
                            <span class="statut-nom"><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                            <span class="statut-valeur"><?= $cat['total'] ?> service<?= $cat['total'] > 1 ? 's' : '' ?></span>
                        </div>
                        <div class="progress-bar-manual mb-3">
                            <div class="progress-fill" style="width: <?= round(($cat['total'] / $max_total) * 100) ?>%;"></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4" style="font-size: 0.85rem;">Aucune catégorie renseignée</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Derniers inscrits -->
        <div class="col-md-12">
            <div class="carte-stats">
                <div class="titre-carte">
                    <i class="fas fa-user-plus me-2" style="color: #c17b4c;"></i> 
                    5 derniers inscrits
                </div>
                
                <?php if(count($derniers_users) > 0): ?>
                    <?php foreach($derniers_users as $u): ?>
                        <div class="user-row">
                            <div>
                                <span style="font-weight: 500;"><?= htmlspecialchars($u['prenom']) ?> <?= htmlspecialchars($u['nom']) ?></span>
                                <span class="badge-stats ms-2"><?= $u['role'] == 'prestataire' ? 'Prestataire' : 'Client' ?></span>
                            </div>
                            <div class="small text-muted">
                                <?= date('d/m/Y', strtotime($u['date_inscription'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-4">Aucun utilisateur pour le moment</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Petit mot du bas, style note perso -->
    <div style="margin-top: 40px; padding: 14px 18px; background: #f0ebe2; border-radius: 16px; border-left: 3px solid #c17b4c;">
        <div style="display: flex; gap: 12px; align-items: flex-start;">
            <i class="fas fa-mug-hot" style="color: #c17b4c; margin-top: 2px;"></i>
            <div>
                <div style="font-weight: 500; font-size: 0.8rem; margin-bottom: 4px;">En un coup d'œil</div>
                <div style="font-size: 0.7rem; color: #8b8a86;">
                    La plateforme compte actuellement <strong><?= $total_users ?> utilisateurs</strong> 
                    et <strong><?= $total_demandes ?> demandes</strong> ont été faites depuis le lancement.
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>