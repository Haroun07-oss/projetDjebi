<?php
require_once 'includes/config.php';
require_once 'includes/security.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

$stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $ville = trim($_POST['ville']);  // NOUVEAU
    
    // MODIFIÉ : ajout de ville dans la requête
    $sql = "UPDATE Utilisateur SET nom=?, prenom=?, telephone=?, ville=? WHERE id_utilisateur=?";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$nom, $prenom, $telephone, $ville, $user_id])) {
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $message = "Profil mis à jour !";
        $user['nom'] = $nom;
        $user['prenom'] = $prenom;
        $user['telephone'] = $telephone;
        $user['ville'] = $ville;  // NOUVEAU
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $ancien = $_POST['ancien_mdp'];
    $nouveau = $_POST['nouveau_mdp'];
    $confirme = $_POST['confirme_mdp'];
    
    if(password_verify($ancien, $user['mot_de_passe'])) {
        if($nouveau == $confirme && strlen($nouveau) >= 4) {
            $hash = password_hash($nouveau, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE Utilisateur SET mot_de_passe=? WHERE id_utilisateur=?")->execute([$hash, $user_id]);
            $message = "Mot de passe modifié !";
        } else {
            $erreur = "Les mots de passe ne correspondent pas ou sont trop courts.";
        }
    } else {
        $erreur = "Ancien mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .carte-profil {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 24px;
            padding: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .input-profil {
            background: #fefcf8;
            border: 1px solid #e2dcd0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
            transition: all 0.15s;
        }
        .input-profil:focus {
            border-color: #c17b4c;
            outline: none;
            background: white;
        }
        .input-disabled {
            background: #f0ebe2;
            border: 1px solid #e2dcd0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
            color: #8b8a86;
        }
        .btn-maj {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-maj:hover {
            background: #a05f38;
        }
        .btn-mdp {
            background: #7c9c8e;
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-mdp:hover {
            background: #5c7c6e;
        }
        .alerte-succes {
            background: #e8f0ec;
            border-left: 3px solid #7c9c8e;
            padding: 12px 16px;
            border-radius: 12px;
            color: #5c7c6e;
            font-size: 0.85rem;
        }
        .alerte-erreur {
            background: #f5e8e5;
            border-left: 3px solid #b87a5a;
            padding: 12px 16px;
            border-radius: 12px;
            color: #b87a5a;
            font-size: 0.85rem;
        }
        .avatar-profil {
            width: 80px;
            height: 80px;
            background: #f0ebe2;
            border-radius: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 600;
            color: #c17b4c;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
    <div class="mb-4 text-center">
        <p style="font-size: 0.7rem; color: #c17b4c; letter-spacing: 1px;">MON COMPTE</p>
        <h1 style="font-size: 1.8rem; font-weight: 600; color: #2c2b28;">Mon profil</h1>
        <p style="color: #8b8a86;">Gérez vos informations personnelles</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-4">
                <div class="avatar-profil">
                    <?= strtoupper(substr($user['prenom'], 0, 1)) ?><?= strtoupper(substr($user['nom'], 0, 1)) ?>
                </div>
                <div class="small text-muted"><i class="far fa-calendar-alt me-1"></i> Membre depuis <?= date('F Y', strtotime($user['date_inscription'])) ?></div>
            </div>
            
            <?php if($message): ?>
                <div class="alerte-succes mb-4">
                    <i class="fas fa-check-circle me-2"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            
            <?php if($erreur): ?>
                <div class="alerte-erreur mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= $erreur ?>
                </div>
            <?php endif; ?>
            
            <div class="carte-profil">
                <div style="font-weight: 600; margin-bottom: 1.2rem; border-left: 3px solid #c17b4c; padding-left: 12px;">
                    <i class="fas fa-user-circle me-2"></i> Informations personnelles
                </div>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Nom</label>
                            <input type="text" name="nom" class="input-profil" value="<?= htmlspecialchars($user['nom']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Prénom</label>
                            <input type="text" name="prenom" class="input-profil" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Email</label>
                        <input type="email" class="input-disabled" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Téléphone</label>
                        <input type="tel" name="telephone" class="input-profil" value="<?= htmlspecialchars($user['telephone']) ?>" required>
                    </div>
                    <!-- NOUVEAU : Champ Ville dans le profil -->
                    <div class="mb-3">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">
                            <i class="fas fa-city me-1"></i> Ville
                        </label>
                        <input type="text" name="ville" class="input-profil" value="<?= htmlspecialchars($user['ville'] ?? '') ?>" placeholder="Votre ville">
                    </div>
                    <div class="text-end">
                        <button type="submit" name="update" class="btn-maj"><i class="fas fa-save me-2"></i> Mettre à jour</button>
                    </div>
                </form>
            </div>
            
            <div class="carte-profil">
                <div style="font-weight: 600; margin-bottom: 1.2rem; border-left: 3px solid #c17b4c; padding-left: 12px;">
                    <i class="fas fa-key me-2"></i> Changer mon mot de passe
                </div>
                <form method="POST">
                    <div class="mb-3">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Ancien mot de passe</label>
                        <input type="password" name="ancien_mdp" class="input-profil" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Nouveau mot de passe</label>
                            <input type="password" name="nouveau_mdp" class="input-profil" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Confirmer</label>
                            <input type="password" name="confirme_mdp" class="input-profil" required>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" name="change_password" class="btn-mdp"><i class="fas fa-unlock-alt me-2"></i> Changer le mot de passe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>