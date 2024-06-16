<?php
session_start();
header('Content-Type: application/json');

// Récupération de l'ID du produit depuis la requête POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Chercher et supprimer le produit du panier
foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['IdProduit'] == $id) {
        unset($_SESSION['cart'][$key]);
        break;
    }
}

// Réindexer le tableau
$_SESSION['cart'] = array_values($_SESSION['cart']);

echo json_encode(['success' => true]);
?>
