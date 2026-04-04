<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

// Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Mettre à jour le profil
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    
    $sql = "UPDATE Utilisateur SET nom = ?, prenom = ?, telephone = ? WHERE id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$nom, $prenom, $telephone, $user_id])) {
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $message = "Profil mis à jour avec succès !";
        // Recharger les données
        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    }
}

// Changer le mot de passe
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirme_mdp = $_POST['confirme_mdp'];
    
    if(!password_verify($ancien_mdp, $user['mot_de_passe'])) {
        $erreur = "Ancien mot de passe incorrect.";
    } elseif($nouveau_mdp !== $confirme_mdp) {
        $erreur = "Les nouveaux mots de passe ne correspondent pas.";
    } elseif(strlen($nouveau_mdp) < 4) {
        $erreur = "Le mot de passe doit faire au moins 4 caractères.";
    } else {
        $nouveau_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE Utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
        if($stmt->execute([$nouveau_hash, $user_id])) {
            $message = "Mot de passe modifié avec succès !";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-user-circle"></i> Mon profil</h4>
                </div>
                <div class="card-body">
                    <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
                    <?php endif; ?>
                    <?php if($erreur): ?>
                        <div class="alert alert-danger"><?= $erreur ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            <small class="text-muted">L'email ne peut pas être modifié</small>
                        </div>
                        
                        <div class="mb-3">
                            <label>Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($user['telephone']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Rôle</label>
                            <input type="text" class="form-control" value="<?= $user['role'] ?>" disabled>
                        </div>
                        
                        <button type="submit" name="update_profil" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </form>
                </div>
            </div>

            <!-- Changement de mot de passe -->
            <div class="card shadow mt-4">
                <div class="card-header bg-warning">
                    <h5><i class="fas fa-key"></i> Changer mon mot de passe</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Ancien mot de passe</label>
                            <input type="password" name="ancien_mdp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nouveau mot de passe</label>
                            <input type="password" name="nouveau_mdp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirmer le nouveau mot de passe</label>
                            <input type="password" name="confirme_mdp" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-sync-alt"></i> Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>