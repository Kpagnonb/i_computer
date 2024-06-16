<?php
session_start();
header('Content-Type: application/json');

// Récupération des données JSON envoyées depuis detail_produit.js
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['type'])) {
    // echo json_encode(['success' => false, 'message' => 'Paramètres id ou type manquants']);
    exit;
}

$id = $data['id'];
$type = $data['type']; 

// Initialisation du panier dans la session s'il n'existe pas encore
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Configuration de la connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=produit;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer les détails du produit en fonction du type
    switch ($type) {
        case 'ordinateur':
            $sql = "SELECT o.IdProduit, o.Nom, o.Prix, o_i.chemin_image 
                    FROM ordinateurs o
                    JOIN images o_i ON o.IdProduit = o_i.id_produit 
                    WHERE o.IdProduit = :id";
            break;
        case 'peripherique_reseau':
            $sql = "SELECT pr.IdProduit, pr.Nom, pr.Prix, ipr.chemin_image 
                    FROM peripheriques_reseaux pr
                    JOIN images_peripheriques_reseaux ipr ON pr.IdProduit = ipr.id_produit 
                    WHERE pr.IdProduit = :id";
            break;
        case 'telephonie':
            $sql = "SELECT t.IdProduit, t.Nom, t.Prix, it.chemin_image 
                    FROM telephonie t
                    JOIN images_telephonie it ON t.IdProduit = it.id_produit 
                    WHERE t.IdProduit = :id";
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Type de produit inconnu']);
            exit;
    }

    // Préparation et exécution de la requête SQL pour récupérer les informations du produit
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si le produit a été trouvé
    if ($product) {
        $product['quantite'] = 1; // Initialisation de la quantité à 1

        // Vérification si le produit est déjà dans le panier
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['IdProduit'] == $product['IdProduit']) {
                $item['quantite']++;
                $found = true;
                break;
            }
        }

        // Si le produit n'est pas encore dans le panier, l'ajouter
        if (!$found) {
            $_SESSION['cart'][] = $product;
        }

        // Calculer le nouveau total d'articles dans le panier
        $cartCount = array_sum(array_column($_SESSION['cart'], 'quantite'));
        echo json_encode(['success' => true, 'cartCount' => $cartCount]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion : ' . $e->getMessage()]);
    exit;
}
?>
