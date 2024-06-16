<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['IdProduit'] == $id) {
                unset($_SESSION['cart'][$key]);
                // Ré-indexer le tableau
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                echo json_encode(['success' => true]);
                exit;
            }
        }
    }
    echo json_encode(['success' => false, 'message' => 'Produit non trouvé dans le panier']);
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
