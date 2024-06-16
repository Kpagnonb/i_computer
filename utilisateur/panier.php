<?php
session_start();

// Inclusion du fichier de connexion aux bases de données
require_once 'connexion_bdd.php';

if (!isset($_SESSION['client_id'])) {
    header('Location: ../connexion.php');
    exit;
}


$client_id = $_SESSION['client_id'];

try {

    // Récupérer les informations du client
    $stmt_client = $pdo_utilisateur->prepare("SELECT * FROM client WHERE id = :id");
    $stmt_client->execute(['id' => $client_id]);
    $client_info = $stmt_client->fetch(PDO::FETCH_ASSOC);

    // Récupérer les articles du panier depuis la session
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    // Parcourir les articles du panier et ajouter le chemin de l'image
    foreach ($cartItems as &$item) {
        $type_produit = isset($item['type_produit']) ? $item['type_produit'] : null;
        $id_produit = $item['IdProduit'];
    
        // Déterminer la table et le chemin d'image en fonction du type de produit
        switch ($type_produit) {
            case 'ordinateur':
                $sql_image = "SELECT MIN(chemin_image) AS image_path FROM images WHERE id_produit = :id_produit";
                break;
            case 'peripherique_reseau':
                $sql_image = "SELECT MIN(chemin_image) AS image_path FROM images_peripheriques_reseaux WHERE id_produit = :id_produit";
                break;
            case 'telephonie':
                $sql_image = "SELECT MIN(chemin_image) AS image_path FROM images_telephonie WHERE id_produit = :id_produit";
                break;
            default:
                $sql_image = "";
                break;
        }
    
        if (!empty($sql_image)) {
            $stmt_image = $pdo_produit->prepare($sql_image);
            $stmt_image->execute(['id_produit' => $id_produit]);
            $image_path = $stmt_image->fetchColumn();
            // Assurez-vous que le chemin d'image est correctement formé
            if ($image_path) {
                // Construire le chemin complet de l'image si nécessaire
                // Par exemple, si $image_path est juste le nom du fichier, vous devez concaténer avec le chemin relatif.
                $item['image_path'] = '../utilisateur/images/' . $image_path; // Assurez-vous que 'images/' est correct selon votre structure de dossier.
            } else {
                // Gérez le cas où aucune image n'est trouvée, peut-être une image par défaut ?
                $item['image_path'] = '../utilisateur/images/default.jpg'; // Chemin vers une image par défaut
            }
        }
    }
    unset($item);
    
    // Suggestions de produits
    $suggestions = [];

    // Suggestions depuis la table ordinateurs
    $sql_ordinateurs = "SELECT 'ordinateur' AS type_produit, o.IdProduit, o.Nom, o.Prix, MIN(i.chemin_image) AS chemin_image 
                        FROM ordinateurs o
                        JOIN images i ON o.IdProduit = i.id_produit
                        GROUP BY o.IdProduit
                        ORDER BY RAND() LIMIT 3";
    $stmt_ordinateurs = $pdo_produit->query($sql_ordinateurs);
    $suggestions = array_merge($suggestions, $stmt_ordinateurs->fetchAll(PDO::FETCH_ASSOC));

    // Suggestions depuis la table peripheriques_reseaux
    $sql_peripheriques = "SELECT 'peripherique_reseau' AS type_produit, pr.IdProduit, pr.Nom, pr.Prix, MIN(ipr.chemin_image) AS chemin_image 
                          FROM peripheriques_reseaux pr
                          JOIN images_peripheriques_reseaux ipr ON pr.IdProduit = ipr.id_produit
                          GROUP BY pr.IdProduit
                          ORDER BY RAND() LIMIT 3";
    $stmt_peripheriques = $pdo_produit->query($sql_peripheriques);
    $suggestions = array_merge($suggestions, $stmt_peripheriques->fetchAll(PDO::FETCH_ASSOC));

    // Suggestions depuis la table telephonie
    $sql_telephonie = "SELECT 'telephonie' AS type_produit, t.IdProduit, t.Nom, t.Prix, MIN(it.chemin_image) AS chemin_image 
                       FROM telephonie t
                       JOIN images_telephonie it ON t.IdProduit = it.id_produit
                       GROUP BY t.IdProduit
                       ORDER BY RAND() LIMIT 3";
    $stmt_telephonie = $pdo_produit->query($sql_telephonie);
    $suggestions = array_merge($suggestions, $stmt_telephonie->fetchAll(PDO::FETCH_ASSOC));

    // Calcul du total
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['Prix'] * $item['quantite'];
    }

    $_SESSION['total'] = $total;

} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innov Invest - Panier</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/panier.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header-logo">
            <img src="logo.png" alt="Logo" class="img-fluid">
        </header>

        <section class="panier-section">
            <h1>Mon Panier</h1>
            <div class="cart-items">
                <?php if (empty($cartItems)): ?>
                    <p>Votre panier est vide.</p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['Nom']) ?>" class="img-thumbnail" style="width: 100px; height: 100px;"></td>
                                    <td><?= htmlspecialchars($item['Nom']) ?></td>
                                    <td><?= htmlspecialchars($item['Prix']) ?> FCFA</td>
                                    <td><?= htmlspecialchars($item['quantite']) ?></td>
                                    <td><?= htmlspecialchars($item['Prix'] * $item['quantite']) ?> FCFA</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="total-section mt-3">
                <h5 class="text-end">Total: <?= htmlspecialchars($total) ?> FCFA</h5>
            </div>
            <a href="paiement.php" class="btn btn-primary mt-3">Valider la commande</a>
        </section>

        <section class="suggestions">
            <h2>Suggestions d'articles</h2>
            <div class="suggestions-container" id="suggestions-container">
                <?php foreach ($suggestions as $suggestion): ?>
                    <div class="suggestion-item">
                        <div class="suggestion-image">
                            <img src="<?= htmlspecialchars($suggestion['chemin_image']) ?>" alt="Image du produit">
                        </div>
                        <h3><?= htmlspecialchars($suggestion['Nom']) ?></h3>
                        <p>Prix: <?= htmlspecialchars($suggestion['Prix']) ?> FCFA</p>
                        <a href="detail_produit.php?id=<?= htmlspecialchars($suggestion['IdProduit']) ?>&type=<?= htmlspecialchars($suggestion['type_produit']) ?>" class="btn btn-primary">Voir détails</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</body>
</html>
