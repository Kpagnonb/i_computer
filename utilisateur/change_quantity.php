<?php
session_start();
header('Content-Type: application/json');

// Récupération de l'ID du produit et de l'action depuis la requête POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$action = $data['action'];

// Chercher le produit dans le panier et ajuster la quantité
foreach ($_SESSION['cart'] as &$item) {
    if ($item['IdProduit'] == $id) {
        if ($action == 'increase') {
            $item['quantite']++;
        } elseif ($action == 'decrease' && $item['quantite'] > 1) {
            $item['quantite']--;
        }
        break;
    }
}

echo json_encode(['success' => true]);
?>
