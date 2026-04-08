<?php
require_once '../includes/config.php';
require_once '../includes/security.php';
requireAdmin();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom_categorie']);
    $desc = trim($_POST['description']);
    if(!empty($nom)) {
        $pdo->prepare("INSERT INTO Categorie (nom_categorie, description) VALUES (?, ?)")->execute([$nom, $desc]);
        header('Location: categories.php');
        exit();
    }
}

if(isset($_GET['supprimer'])) {
    $pdo->prepare("DELETE FROM Categorie WHERE id_categorie = ?")->execute([$_GET['supprimer']]);
    header('Location: categories.php');
    exit();
}

$categories = $pdo->query("SELECT * FROM Categorie ORDER BY nom_categorie")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles maison - pas de template ici */
        .page-categorie {
            background: #f4f1ea;
        }
        
        .carte-form {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            box-shadow: 6px 6px 0 rgba(0,0,0,0.03);
        }
        
        .titre-section {
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: -0.3px;
            border-left: 3px solid #c17b4c;
            padding-left: 12px;
            margin-bottom: 1.2rem;
        }
        
        .input-maison {
            background: #fefcf8;
            border: 1px solid #e0d9cd;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 0.9rem;
            transition: all 0.15s;
        }
        
        .input-maison:focus {
            border-color: #c17b4c;
            outline: none;
            background: white;
            box-shadow: none;
        }
        
        .btn-ajout {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 10px 20px;
            font-weight: 500;
            color: white;
            width: 100%;
            transition: all 0.15s;
        }
        
        .btn-ajout:hover {
            background: #a05f38;
            transform: translateY(-1px);
        }
        
        .element-categorie {
            background: white;
            border: 1px solid #ece5d8;
            border-radius: 16px;
            padding: 14px 18px;
            margin-bottom: 10px;
            transition: all 0.15s;
        }
        
        .element-categorie:hover {
            border-color: #c17b4c;
            background: #fffef9;
        }
        
        .nom-categorie {
            font-weight: 600;
            font-size: 1rem;
            color: #2c2b28;
        }
        
        .desc-categorie {
            font-size: 0.8rem;
            color: #8b8a86;
            margin-top: 5px;
            line-height: 1.4;
        }
        
        .btn-suppr {
            background: transparent;
            border: 1px solid #e8dfd3;
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 0.75rem;
            color: #b87a5a;
            transition: all 0.15s;
        }
        
        .btn-suppr:hover {
            background: #faf0ea;
            border-color: #c17b4c;
            color: #a05f38;
        }
        
        .compteur-badge {
            background: #f0ebe2;
            color: #6b6a67;
            font-size: 0.7rem;
            padding: 3px 9px;
            border-radius: 40px;
            font-weight: 500;
        }
        
        .separateur {
            border-top: 1px solid #e8e0d4;
            margin: 20px 0;
        }
        
        @media (max-width: 768px) {
            .element-categorie {
                padding: 12px 14px;
            }
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    
    <!-- Entête simple, pas de fioritures -->
    <div class="mb-5">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-tag" style="color: #c17b4c; font-size: 1.3rem;"></i>
            <span style="font-size: 0.75rem; color: #b5a88e; letter-spacing: 1px;">ADMIN</span>
        </div>
        <h1 style="font-size: 2rem; font-weight: 600; letter-spacing: -0.5px; color: #2c2b28;">Catégories</h1>
        <p style="color: #8b8a86; margin-top: 8px;">Organisez les services par domaine</p>
    </div>
    
    <div class="row g-4">
        
        <!-- Formulaire d'ajout - côté gauche -->
        <div class="col-md-5">
            <div class="carte-form p-4">
                <div class="titre-section">
                    <i class="fas fa-plus me-2" style="color: #c17b4c; font-size: 0.85rem;"></i> 
                    Nouvelle catégorie
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">
                            Nom
                        </label>
                        <input type="text" name="nom_categorie" class="input-maison w-100" placeholder="Ex: Plomberie" required>
                    </div>
                    
                    <div class="mb-4">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">
                            Description (optionnel)
                        </label>
                        <textarea name="description" class="input-maison w-100" rows="3" placeholder="Décrivez brièvement cette catégorie..."></textarea>
                    </div>
                    
                    <button type="submit" name="ajouter" class="btn-ajout">
                        <i class="fas fa-save me-2"></i> Ajouter
                    </button>
                </form>
                
                <div class="separateur"></div>
                
                <div class="small text-muted" style="font-size: 0.7rem; color: #b5a88e;">
                    <i class="fas fa-info-circle me-1"></i> Une fois ajoutée, la catégorie sera visible par tous
                </div>
            </div>
        </div>
        
        <!-- Liste des catégories - côté droite -->
        <div class="col-md-7">
            <div class="carte-form p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="titre-section" style="margin-bottom: 0;">
                        <i class="fas fa-list me-2" style="color: #c17b4c;"></i> 
                        Toutes les catégories
                    </div>
                    <div class="compteur-badge">
                        <?= count($categories) ?> élément<?= count($categories) > 1 ? 's' : '' ?>
                    </div>
                </div>
                
                <?php if(count($categories) == 0): ?>
                    <div style="text-align: center; padding: 40px 20px;">
                        <i class="fas fa-folder-open" style="font-size: 2rem; color: #d4cdbe; margin-bottom: 12px; display: block;"></i>
                        <p style="color: #b5a88e; margin: 0;">Aucune catégorie pour le moment</p>
                        <p style="color: #d4cdbe; font-size: 0.75rem;">Ajoutez-en une avec le formulaire</p>
                    </div>
                <?php else: ?>
                    <div style="max-height: 500px; overflow-y: auto; padding-right: 5px;">
                        <?php foreach($categories as $c): ?>
                            <div class="element-categorie">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="flex: 1;">
                                        <div class="nom-categorie">
                                            <?= htmlspecialchars($c['nom_categorie']) ?>
                                        </div>
                                        <?php if($c['description']): ?>
                                            <div class="desc-categorie">
                                                <?= htmlspecialchars($c['description']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <a href="?supprimer=<?= $c['id_categorie'] ?>" 
                                           class="btn-suppr" 
                                           onclick="return confirm('Supprimer « <?= addslashes($c['nom_categorie']) ?> » ?')"
                                           style="text-decoration: none;">
                                            <i class="fas fa-trash-alt me-1"></i> Suppr
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Petit rappel en bas, style "notes perso" -->
    <div style="margin-top: 40px; padding: 12px 16px; background: #f0ebe2; border-radius: 14px; border-left: 3px solid #c17b4c;">
        <div style="font-size: 0.75rem; color: #8b8a86;">
            <i class="fas fa-pencil-alt me-2" style="color: #c17b4c;"></i>
            Les catégories aident les clients à trouver plus facilement ce qu'ils cherchent.
        </div>
    </div>
    
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>