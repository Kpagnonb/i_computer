<?php
session_start();

// Vérifier la méthode de requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si les paramètres requis sont présents
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id']) && isset($data['quantity'])) {
        $productId = $data['id'];
        $newQuantity = $data['quantity'];

        // Vérifier si le panier existe dans la session
        if (isset($_SESSION['cart'])) {
            // Parcourir le panier pour mettre à jour la quantité de l'article spécifié
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['IdProduit'] == $productId) {
                    $item['quantite'] = $newQuantity;
                    break;
                }
            }
        }

        // Retourner une réponse JSON indiquant le succès de la mise à jour
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Si les paramètres requis ne sont pas présents, retourner une erreur
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
        exit;
    }
}
?>
