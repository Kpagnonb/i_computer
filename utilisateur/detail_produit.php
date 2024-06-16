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

    // Vérifier si l'ID du produit et le type de produit sont passés en paramètre
    if (!isset($_GET['id']) || !isset($_GET['type'])) {
        echo "ID ou type du produit manquant";
            exit;
    }

    $id_produit = $_GET['id'];
    $type_produit = $_GET['type'];

    // Déterminer la table et les champs spécifiques en fonction du type de produit
    switch ($type_produit) {
        case 'ordinateur':
            $table = 'ordinateurs';
                $images_table = 'images';
            break;
        case 'peripherique_reseau':
            $table = 'peripheriques_reseaux';
            $images_table = 'images_peripheriques_reseaux';
            break;
        case 'telephonie':
            $table = 'telephonie';
            $images_table = 'images_telephonie';
            break;
        default:
            echo "Type de produit inconnu";
            exit;
    }

    // Requête pour récupérer les détails du produit
    $sql = "SELECT * FROM $table WHERE IdProduit = :id";
    $stmt = $pdo_produit->prepare($sql);
    $stmt->execute(['id' => $id_produit]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        echo "Produit non trouvé";
        exit;
    }

    // Requête pour récupérer les images associées au produit
    $sql_images = "SELECT chemin_image FROM $images_table WHERE id_produit = :id";
    $stmt_images = $pdo_produit->prepare($sql_images);
    $stmt_images->execute(['id' => $id_produit]);
    $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

    // Si l'action d'ajout au panier est demandée, ajouter le produit au panier
    if (isset($_GET['action']) && $_GET['action'] === 'add_to_cart') {
        // Ajouter le produit au panier
        $cartItem = [
            'IdProduit' => $id_produit,
            'type_produit' => $type_produit,
            'Nom' => $produit['Nom'],
            'Prix' => $produit['Prix'],
            // Ajoutez d'autres informations si nécessaire
        ];

        // Initialiser $_SESSION['cart'] si ce n'est pas déjà fait
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Ajouter l'article au panier
        $_SESSION['cart'][] = $cartItem;

    }

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

} catch (PDOException $e) {
    echo 'Erreur PDO : ' . $e->getMessage();  // Affichez l'erreur PDO spécifique
    exit;
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();  // Capturez toute autre exception
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Produit</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/style.css">
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

    <section class="product-detail">
        <div class="carousel-container">
            <div class="carousel">
                <?php foreach ($images as $index => $image): ?>
                    <img src="<?= htmlspecialchars($image['chemin_image']) ?>" alt="Image du produit" class="carousel-image" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                <?php endforeach; ?>
            </div>
            <div class="thumbnails">
                <?php foreach ($images as $index => $image): ?>
                    <img src="<?= htmlspecialchars($image['chemin_image']) ?>" alt="Thumbnail" class="thumbnail <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="product-info">
            <h2 id="product-title"><?= htmlspecialchars($produit['Nom']) ?></h2>
            <div class="features">
                <h3>Caractéristiques Principales</h3>
                <?php if ($type_produit == 'ordinateur'): ?>
                    <ul>
                        <li>Modèle : <?= htmlspecialchars($produit['Modele']) ?></li>
                        <li>Processeur : <?= htmlspecialchars($produit['Processeur']) ?></li>
                        <li>Mémoire RAM : <?= htmlspecialchars($produit['RAM']) ?></li>
                        <li>Stockage : <?= htmlspecialchars($produit['Stockage']) ?></li>
                        <li>Carte graphique : <?= htmlspecialchars($produit['GPU']) ?></li>
                        <li>Système d'exploitation : <?= htmlspecialchars($produit['OS']) ?></li>
                    </ul>
                <?php elseif ($type_produit == 'peripherique_reseau'): ?>
                    <ul>
                        <li>Marque : <?= htmlspecialchars($produit['Marque']) ?></li>
                        <li>TypeProduit : <?= htmlspecialchars($produit['TypeProduit']) ?></li>
                    </ul>
                <?php elseif ($type_produit == 'telephonie'): ?>
                    <ul>
                        <li>Marque : <?= htmlspecialchars($produit['Marque']) ?></li>
                        <li>TypeProduit : <?= htmlspecialchars($produit['TypeProduit']) ?></li>
                    </ul>
                <?php else: ?>
                    <p>Aucune caractéristique spécifique disponible pour ce type de produit.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="price-add-to-cart">
            <p class="price"><?= htmlspecialchars($produit['Prix']) ?></p>
            <button id="add-to-cart" class="btn btn-primary add-to-cart" data-id="<?= htmlspecialchars($produit['IdProduit']) ?>" data-type="<?= htmlspecialchars($type_produit) ?>">
                <i class="bi bi-cart cart-icon"></i> Ajouter au panier
            </button>
        </div>
    </section>

    <div class="product-description">
        <h3>Présentation du <?= htmlspecialchars($produit['Nom']) ?></h3>
        <p><?= nl2br(htmlspecialchars($produit['Description'])) ?></p>
    </div>

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

    <script src="js/detail_produit.js"></script>
    <script src="js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const type = this.dataset.type; // Assurez-vous que vous récupérez le type ici

                console.log("ID:", id);
                console.log("Type:", type);

                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, type }) // Passez le type à add_to_cart.php
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Response data:", data);
                    if (data.success) {
                        document.querySelector('.cart-count').textContent = data.cartCount;
                    } else {
                        // alert('Erreur lors de l\'ajout au panier : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            });
        });
        
        function toggleAccountMenu() {
            const accountDropdown = document.querySelector('.account-dropdown');
            accountDropdown.classList.toggle('active');
        }
    </script>
</body>
</html>
