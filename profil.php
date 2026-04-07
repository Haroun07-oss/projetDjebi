<?php
require_once 'includes/config.php';
require_once 'includes/security.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';

$stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    
    $sql = "UPDATE Utilisateur SET nom=?, prenom=?, telephone=? WHERE id_utilisateur=?";
    $stmt = $pdo->prepare($sql);
    if($stmt->execute([$nom, $prenom, $telephone, $user_id])) {
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_prenom'] = $prenom;
        $message = "Profil mis à jour !";
        $user['nom'] = $nom;
        $user['prenom'] = $prenom;
        $user['telephone'] = $telephone;
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
    <title>Mon profil - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i> Mon profil
                </div>
                <div class="card-body">
                    <?php if($message): ?>
                        <div class="alert alert-success"><?= $message ?></div>
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
                        </div>
                        <div class="mb-3">
                            <label>Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($user['telephone']) ?>" required>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-warning">
                    <i class="fas fa-key"></i> Changer mon mot de passe
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
                            <label>Confirmer</label>
                            <input type="password" name="confirme_mdp" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">Changer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>