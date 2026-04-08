<?php
require_once '../includes/config.php';

if(isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$erreur = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    
    $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        if($user['statut_compte'] == 'actif') {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit();
        } else {
            $erreur = "Compte désactivé. Contactez l'administrateur.";
        }
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - IvoireBara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #f4f1ea;
        }
        .carte-connexion {
            background: #fffef7;
            border: 1px solid #e2dcd0;
            border-radius: 28px;
            padding: 2rem;
            max-width: 450px;
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
        .btn-connexion {
            background: #c17b4c;
            border: none;
            border-radius: 40px;
            padding: 12px;
            width: 100%;
            color: white;
            font-weight: 500;
            transition: all 0.15s;
        }
        .btn-connexion:hover {
            background: #a05f38;
        }
        .lien-inscription {
            color: #c17b4c;
            text-decoration: none;
        }
        .lien-inscription:hover {
            text-decoration: underline;
        }
        .alerte-erreur {
            background: #f5e8e5;
            border-left: 3px solid #b87a5a;
            padding: 12px 16px;
            border-radius: 12px;
            color: #b87a5a;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-4">
        <a href="../index.php" style="text-decoration: none;">
            <h2 style="color: #2c2b28; font-weight: 700;">Ivoire<span style="color: #c17b4c;">Bara</span></h2>
        </a>
    </div>
    
    <div class="carte-connexion">
        <div class="text-center mb-4">
            <i class="fas fa-handshake fa-2x" style="color: #c17b4c;"></i>
            <h3 style="font-weight: 600; margin-top: 10px;">Connexion</h3>
            <p style="color: #8b8a86; font-size: 0.85rem;">Ravis de vous revoir 👋</p>
        </div>
        
        <?php if($erreur): ?>
            <div class="alerte-erreur mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $erreur ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Email</label>
                <input type="email" name="email" class="input-auth" required>
            </div>
            <div class="mb-4">
                <label style="font-size: 0.8rem; font-weight: 500; color: #5c5b58; margin-bottom: 6px; display: block;">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="input-auth" required>
            </div>
            <button type="submit" class="btn-connexion">Se connecter</button>
        </form>
        
        <div class="text-center mt-4" style="font-size: 0.85rem; color: #8b8a86;">
            Pas encore de compte ? <a href="register.php" class="lien-inscription">Inscrivez-vous</a>
        </div>
    </div>
</div>

</body>
</html>