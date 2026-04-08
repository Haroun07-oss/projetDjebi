<?php
// Configuration de la base de données
define('BASE_URL', 'http://localhost/projetDjebi');

$host = 'localhost';
$dbname = 'plateforme_services';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

session_start();
?>