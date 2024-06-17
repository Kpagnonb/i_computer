<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    $action = $input['action'];

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['IdProduit'] == $id) {
                if ($action === 'increment') {
                    $item['quantite']++;
                } elseif ($action === 'decrement') {
                    if ($item['quantite'] > 1) {
                        $item['quantite']--;
                    } else {
                        // Optionnel : Supprimer l'article si la quantité est réduite à zéro
                        unset($item);
                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                    }
                }
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
