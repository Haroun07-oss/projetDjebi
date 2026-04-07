<?php
// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Vérifier le rôle
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role;
}

// Rediriger si non connecté
function requireLogin() {
    if(!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit();
    }
}

// Rediriger si pas admin
function requireAdmin() {
    requireLogin();
    if(!hasRole('admin')) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
}

// Rediriger si pas prestataire
function requirePrestataire() {
    requireLogin();
    if(!hasRole('prestataire')) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
}

// Rediriger si pas client
function requireClient() {
    requireLogin();
    if(!hasRole('client')) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
}
?>