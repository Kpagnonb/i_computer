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

    // Requête pour récupérer les informations du client depuis la base de données utilisateur
    $sql_client = "SELECT * FROM client WHERE id = :id";
    $stmt_client = $pdo_utilisateur->prepare($sql_client);
    $stmt_client->execute([':id' => $client_id]);
    $client = $stmt_client->fetch(PDO::FETCH_ASSOC);


    // Requête pour récupérer tous les ordinateurs et une image associée
    $sql_produits = "
        SELECT 
            'ordinateur' AS type_produit,
            o.IdProduit,
            o.Nom,
            o.Processeur AS attribut_specifique,
            o.RAM AS autre_attribut,
            o.Prix,
            MAX(i.chemin_image) AS chemin_image
        FROM ordinateurs o
        LEFT JOIN images i ON o.IdProduit = i.id_produit
        GROUP BY o.IdProduit

        UNION

        SELECT 
            'peripherique_reseau' AS type_produit,
            pr.IdProduit,
            pr.Nom,
            '' AS attribut_specifique,
            '' AS autre_attribut,
            pr.Prix,
            MAX(ipr.chemin_image) AS chemin_image
        FROM peripheriques_reseaux pr
        LEFT JOIN images_peripheriques_reseaux ipr ON pr.IdProduit = ipr.id_produit
        GROUP BY pr.IdProduit

        UNION

        SELECT 
            'telephonie' AS type_produit,
            t.IdProduit,
            t.Nom,
            '' AS attribut_specifique,
            '' AS autre_attribut,
            t.Prix,
            MAX(it.chemin_image) AS chemin_image
        FROM telephonie t
        LEFT JOIN images_telephonie it ON t.IdProduit = it.id_produit
        GROUP BY t.IdProduit
    ";

    $stmt_produits = $pdo_produit->query($sql_produits); // Utilisation de $pdo pour les produits
    $products = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);
    
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
    <title>Innov Invest - I Computer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
    <header>
        <div class="header-content">
            <h1>Innov Invest</h1>
        </div>
        <div class="header-toolbar">
            <div class="search-bar">
                <input type="text" placeholder="Rechercher des produits..." id="searchInput">
                <button type="button" onclick="searchProducts()">Rechercher</button>
            </div>
            <div class="account-menu">
                <a href="#" onclick="toggleAccountMenu()">
                    <i class="bi bi-person-circle"></i>
                    <span><?= htmlspecialchars($client['nom']) ?></span>
                </a>
                <div class="account-dropdown">
                    <ul>
                        <li><a href="#">Mon Compte</a></li>
                        <li><a href="../deconnexion.php">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
            <span class="cart-container">
                <a href="panier.php" onclick="showCart()">
                    <i class="bi bi-cart cart-icon"></i>
                    <span class="cart-count">0</span>
                </a>
            </span>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="produit.php">Produits</a></li>
                <li><a href="panier.php">Panier</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="product-list">
            <div class="products-container">
                <h2 class="products-heading">Nos Produits</h2>

                <!-- Section pour les ordinateurs -->
                <section class="products">
                    <h3>Ordinateurs</h3>
                    <?php foreach ($products as $product): ?>
                        <?php if ($product['type_produit'] === 'ordinateur'): ?>
                            <div class="product">
                                <!-- Contenu de chaque produit -->
                                <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                    <img src="<?= htmlspecialchars($product['chemin_image'] ?: 'images/default.jpg') ?>" 
                                        alt="<?= htmlspecialchars($product['Nom']) ?>">
                                    <h3 class="product-title"><?= htmlspecialchars($product['Nom']) ?></h3>
                                </a>
                                <p>Prix: <?= htmlspecialchars($product['Prix']) ?> FCFA</p>
                                <div class="price-add-to-cart">
                                    <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                        <i class="bi bi-info-circle"></i> Détails du produit
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </section>

                <!-- Section pour les périphériques réseau -->
                <section class="products">
                    <h3>Périphériques Réseau</h3>
                    <?php foreach ($products as $product): ?>
                        <?php if ($product['type_produit'] === 'peripherique_reseau'): ?>
                            <div class="product">
                                <!-- Contenu de chaque produit -->
                                <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                    <img src="<?= htmlspecialchars($product['chemin_image'] ?: 'images/default.jpg') ?>" 
                                        alt="<?= htmlspecialchars($product['Nom']) ?>">
                                    <h3 class="product-title"><?= htmlspecialchars($product['Nom']) ?></h3>
                                </a>
                                <p>Prix: <?= htmlspecialchars($product['Prix']) ?> FCFA</p>
                                <div class="price-add-to-cart">
                                    <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                        <i class="bi bi-info-circle"></i> Détails du produit
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </section>

                <!-- Section pour la téléphonie -->
                <section class="products">
                    <h3>Téléphonie</h3>
                    <?php foreach ($products as $product): ?>
                        <?php if ($product['type_produit'] === 'telephonie'): ?>
                            <div class="product">
                                <!-- Contenu de chaque produit -->
                                <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                    <img src="<?= htmlspecialchars($product['chemin_image'] ?: 'images/default.jpg') ?>" 
                                        alt="<?= htmlspecialchars($product['Nom']) ?>">
                                    <h3 class="product-title"><?= htmlspecialchars($product['Nom']) ?></h3>
                                </a>
                                <p>Prix: <?= htmlspecialchars($product['Prix']) ?> FCFA</p>
                                <div class="price-add-to-cart">
                                    <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                        <i class="bi bi-info-circle"></i> Détails du produit
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </section>

            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Innov Invest. Tous droits réservés.</p>
    </footer>
 
    <script src="js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleAccountMenu() {
            const accountDropdown = document.querySelector('.account-dropdown');
            accountDropdown.classList.toggle('active');
        }
     </script>
</body>
</body>
</html>
