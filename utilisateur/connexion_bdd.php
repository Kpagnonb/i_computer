<?php

// Configuration de la base de données utilisateur
$dsn_utilisateur = 'mysql:host=localhost;dbname=utilisateur;charset=utf8';
$username_utilisateur = 'root';
$password_utilisateur = '';

// Options de connexion à la base de données utilisateur
$options_utilisateur = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Configuration de la base de données produit
$dsn_produit = 'mysql:host=localhost;dbname=produit;charset=utf8';
$username_produit = 'root';
$password_produit = '';

// Options de connexion à la base de données produit
$options_produit = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Connexion à la base de données utilisateur
    $pdo_utilisateur = new PDO($dsn_utilisateur, $username_utilisateur, $password_utilisateur, $options_utilisateur);

    // Connexion à la base de données produit
    $pdo_produit = new PDO($dsn_produit, $username_produit, $password_produit, $options_produit);

} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher l'erreur
    echo 'Erreur de connexion : ' . $e->getMessage();
    exit();
}
?>
