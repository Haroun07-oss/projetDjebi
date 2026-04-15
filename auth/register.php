<?php
require_once '../includes/config.php';

if(isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$message = '';
$erreur = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $ville = trim($_POST['ville']);  // NOUVEAU
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirme = $_POST['confirme_mdp'];
    $role = $_POST['role'];
    
    if($mot_de_passe !== $confirme) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            // MODIFIÉ : ajout de ville dans la requête
            $sql = "INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, telephone, ville, role, statut_compte) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'actif')";
            $stmt = $pdo->prepare($sql);
            // MODIFIÉ : ajout de $ville dans execute
            if($stmt->execute([$nom, $prenom, $email, $hash, $telephone, $ville, $role])) {
                $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $erreur = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .carte-inscription {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 28px;
            padding: 2rem;
            max-width: 550px;
            margin: 0 auto;
        }
        .input-auth {
            background: #fefcf8;
            border: 1px solid #e2dcd0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
            transition: all 0.15s;
        }
        .input-auth:focus {
            border-color: #c17b4c;
            outline: none;
            background: white;
        }
        .btn-inscription {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 12px;
            width: 100%;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-inscription:hover {
            background: #a05f38;
        }
        .lien-connexion {
            color: #c17b4c;
            text-decoration: none;
        }
        .lien-connexion:hover {
            text-decoration: underline;
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
        .role-select {
            background: #fefcf8;
            border: 1px solid #e2dcd0;
            border-radius: 14px;
            padding: 12px 16px;
            width: 100%;
        }
    </style>
</head>
<body style="background: #f4f1ea;">

<div class="container py-5">
    <div class="text-center mb-4">
        <a href="../index.php" style="text-decoration: none;">
            <h2 style="color: #2c2b28; font-weight: 700;">Ivoire<span style="color: #c17b4c;">Bara</span></h2>
        </a>
    </div>
    
    <div class="carte-inscription">
        <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-2x" style="color: #c17b4c;"></i>
            <h3 style="font-weight: 600; margin-top: 10px;">Inscription</h3>
            <p style="color: #8b8a86; font-size: 0.85rem;">Rejoignez la communauté !</p>
        </div>
        
        <?php if($message): ?>
            <div class="alerte-succes mb-4">
                <i class="fas fa-check-circle me-2"></i> <?= $message ?>
                <div class="mt-2"><a href="login.php" class="lien-connexion">Se connecter maintenant →</a></div>
            </div>
        <?php endif; ?>
        
        <?php if($erreur): ?>
            <div class="alerte-erreur mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $erreur ?>
            </div>
        <?php endif; ?>
        
        <?php if(!$message): ?>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Nom</label>
                    <input type="text" name="nom" class="input-auth" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Prénom</label>
                    <input type="text" name="prenom" class="input-auth" required>
                </div>
            </div>
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Email</label>
                <input type="email" name="email" class="input-auth" required>
            </div>
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Téléphone</label>
                <input type="tel" name="telephone" class="input-auth" required>
            </div>
            <!-- NOUVEAU : Champ Ville -->
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">
                    <i class="fas fa-city me-1"></i> Ville
                </label>
                <input type="text" name="ville" class="input-auth" placeholder="Ex: Abidjan, Bouaké, Yamoussoukro...">
            </div>
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="input-auth" required>
            </div>
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Confirmer</label>
                <input type="password" name="confirme_mdp" class="input-auth" required>
            </div>
            <div class="mb-4">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Je suis</label>
                <select name="role" class="role-select" required>
                    <option value="client">👤 Client - Je cherche des services</option>
                    <option value="prestataire">🔧 Prestataire - Je propose mes services</option>
                </select>
            </div>
            <button type="submit" class="btn-inscription">S'inscrire</button>
        </form>
        
        <div class="text-center mt-4" style="font-size: 0.85rem; color: #8b8a86;">
            Déjà un compte ? <a href="login.php" class="lien-connexion">Connectez-vous</a>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>