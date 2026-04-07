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
            $sql = "INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, telephone, role, statut_compte) 
                    VALUES (?, ?, ?, ?, ?, ?, 'actif')";
            $stmt = $pdo->prepare($sql);
            if($stmt->execute([$nom, $prenom, $email, $hash, $telephone, $role])) {
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
    <title>Inscription - ServiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4><i class="fas fa-user-plus"></i> Inscription</h4>
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
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Mot de passe</label>
                            <input type="password" name="mot_de_passe" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirmer</label>
                            <input type="password" name="confirme_mdp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Je suis</label>
                            <select name="role" class="form-select" required>
                                <option value="client">Client</option>
                                <option value="prestataire">Prestataire</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>
                    <div class="text-center mt-3">
                        Déjà un compte ? <a href="login.php">Connectez-vous</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>