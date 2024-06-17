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
    <link rel="stylesheet" href="css/stylea.css">
    <style>
        .barre{
            width: 100%;
            height: 50px;
            background-color: #333;
        }
        .grand2{
            display: flex;
            flex-direction: row;
            
        }
        .sous1{
            display: flex;
            flex-direction: column;
            
        }
        .sous{
            color: white;
            text-align: center;
        }
        .grand1{
            background: black;
            width: 450px;
            height: 450px;
            margin-top: -50px;
            color: white;
            padding: 40px;
        }
        .miniblock{
        width: 40px;
        height: 40px;
        }
        .miniblock:hover{
        border: 1px solid rgb(182, 178, 178);
        border-radius: 10px;
        }
        .miniblock2{
        width: 90px;
        height: 40px;
        border-radius: 15px;
        border: 1px solid black;
        background-color: black;
        
        }
        /* button */
        .button {
            display: inline-block;
            padding: 12px 24px;
            border: 1px solid #4f4f4f;
            border-radius: 4px;
            transition: all 0.2s ease-in;
            position: relative;
            overflow: hidden;
            font-size: 19px;
            cursor: pointer;
            color: black;
            z-index: 1;
            width: 200px;
            margin-top: 50px;
            background-color: #f3efef
            ;
            text-transform: capitalize;
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        }
        .button:before {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scaleY(1) scaleX(1.25);
            top: 100%;
            width: 140%;
            height: 180%;
            background-color: rgba(0, 0, 0, 0.05);
            border-radius: 50%;
            display: block;
            transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
            z-index: -1;
        }
        .button:after {
            content: "";
            position: absolute;
            left: 55%;
            transform: translateX(-50%) scaleY(1) scaleX(1.45);
            top: 180%;
            width: 160%;
            height: 190%;
            background-color: #9d1cba;
            border-radius: 50%;
            display: block;
            transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
            z-index: -1;
        }
        .button:hover {
            color: #ffffff;
            border: 1px solid #9d1cba;
        }
        .button:hover:before {
            top: -35%;
            background-color: #9d1cba;
            transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
        }
        .button:hover:after {
            top: -45%;
            background-color: #9d1cba;
            transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
        }
        /* pour les svg  */
        .svg:hover{
            transform: translateY(-10px);
            transition: 2s;
        
        }
        .block-sous{
            display: flex;
            flex-direction: column;
        }
        body{
            background-color: #f3efef;
        }
            .text2{
            color: black;
        }
        a{
            list-style-type: none;
            text-decoration: none;
        }
        .account-menu {
            position: relative;
            display: flex;
            align-items: center;
            color: white;
            z-index: 10;
            margin-right: 15px;
        }
        .account-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: rgb(7, 7, 61);
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 20;
        }
        .account-dropdown.active {
            display: block; 
        }
        .account-dropdown ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .account-dropdown li {
            padding: 10px;
        }
        .account-dropdown li a {
            color: white;
            text-decoration: none;
        }
        .cart-container {
            position: relative;
            margin-left: auto; 
            margin-right: 20px; 
        }
        .cart-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
            margin-right: 5px;
        }
        .cart-count {
            display: inline-block;
            position: absolute;
            top: -10px;
            right: 25px; /* Ajuste la position du compteur de panier à droite */
            background: #FF073A;
            color: #FFFFFF;
            border-radius: 50%;
            padding: 4px 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .product-list {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .product-list .products-heading {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .product-list .products {
            margin-bottom: 40px; /* Ajout d'une marge entre les sections */
        }

        .product-list .products h3 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #444;
            background-color: #e9e9e9;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            position: relative; /* Permet de positionner le titre */
            top: -20px; /* Ajustez cette valeur selon vos besoins */
            z-index: 1;
        }

        .product-list .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 colonnes fixes */
            gap: 20px;
        }

        .product-list .product {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            background-color: #ffffff;
            transition: transform 0.2s ease-in-out;
            text-align: center; /* Centrer le contenu du produit */
        }

        .product-list .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-list .product img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .product-list .product-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .product-list .product p {
            font-size: 16px;
            color: #555;
        }

        .product-list .price-add-to-cart {
            margin-top: 10px;
        }

        .product-list .price-add-to-cart a {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .product-list .price-add-to-cart a:hover {
            background-color: #0056b3;
        }
        /* @media (max-width: 1068px) {
            .header{
                height: 200px;
            }
                
                .carousel-item {
                display: flex;
                flex-direction: column-reverse;
                width: 100%;

                }
            .text{
                font-size: 1rem;
                text-align: center;
                width: 100%;
            }
            .soustext{
            color: #2145e7;
            font-size: 0.5rem;
            width: 100%;

            }
            .sousp{
            text-align: center;
            width: 500px;
            }
        } */
    </style>
</head>
<body>
    
    <header>
        <!-- pour la petite barre -->
        <div class="barre">
            <!-- réseaux sociaux -->
            <ul class="ul">
                <li class="li">
                    <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                </li>
                <li class="li">
                    <a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                </li>
                <li class="li">
                    <a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                </li>
            </ul>
        </div>

        <!-- la navbar -->
        <nav class="navbar navbar-expand-lg" style="background-color: rgb(7, 7, 61);display: flex;width: 100%;">
            <div class="container-fluid" style="display: flex;justify-content: space-between;width: 100%;flex-direction: row;">
                <a class="navbar-brand" href="#" style="color: white;"><b>NOTRE ENTREPRISE</b></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent" style="margin-left: 25%;">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link " aria-current="page" href="index.php" style="color: white;"><b>Accueil</b></a>
                        </li>
                        <li class="nav-item active" >
                            <a class="nav-link" href="produit.php" style="color: white;"><b>Produits</b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="panier.php" style="color: white;"><b>Panier</b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" style="color: white;"><b>A propos</b></a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
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
                    <span class="cart-container ms-3">
                        <a href="panier.php" onclick="showCart()">
                            <i class="bi bi-cart cart-icon"></i>
                            <span class="cart-count">0</span>
                        </a>
                    </span>
                </div>
            </div>
        </nav>

        <div class="row d-none d-xl-block d-xxl-none">
            <div class="header-" style="background-color: black;color: white;height: 700px;align-items: center;justify-content: center;display: flex;">
                    <!-- carrousel de header xl -->
                <div id="carouselExample" class="carousel slide" style="width: 100%;height: 600px;align-items: center;">
                    <div class="carousel-inner" style="width: 100%;">
                        <div class="carousel-item active" style="display: flex;justify-content: space-evenly;width: 100%;margin: 20px;">
                            <div class="col-xl-6 ">
                                <div class="text">
                                    <span>
                                        <h1 class="soustext" style="font-size: 5rem;margin: 20px;">
                                            <b>INNOVSHOP</b>
                                        </h1>
                                        <p class="sousp" style="width: 400px;margin: 20px;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum optio architecto iure unde minima molestias rem labore, tenetur ex voluptate officiis! Debitis quos cupiditate laudantium, ullam maxime reprehenderit harum nesciunt.</p>
                                        <button type="submit" style="margin: 20px;border: 1px solid white;background-color: none;background: none;width: 150px;height: 60px;color: white;text-transform: capitalize;"><b>nous connaitre</b></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xl-6 ">
                            <img src="../utilisateur/images/42104cfab7adc4d7cf53145067445fb5.gif" class="d-block" alt="img" width="700px">
                        </div>
                </div>
            </div>
        </div>
    </header> 
    <main>
        <section class="product-list">
            <div class="products-container">
                <h2 class="products-heading">Nos Produits</h2>

                <!-- Section pour les ordinateurs PC -->
                <div class="products-section">
                    <h3>Ordinateur </h3>
                    <section class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <?php if ($product['type_produit'] === 'ordinateur'): ?>
                                <div class="product">
                                    <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                        <img src="<?= htmlspecialchars($product['chemin_image'] ?: 'images/default.jpg') ?>" alt="<?= htmlspecialchars($product['Nom']) ?>">
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

                <!-- Section pour les souris et périphériques -->
                <div class="products-section">
                    <h3>Souris et Périphériques</h3>
                    <section class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <?php if ($product['type_produit'] === 'peripherique_reseau'): ?>
                                <div class="product">
                                    <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                        <img src="<?= htmlspecialchars($product['chemin_image'] ?: 'images/default.jpg') ?>" alt="<?= htmlspecialchars($product['Nom']) ?>">
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

                <!-- Section pour les PC non portables -->
                <div class="products-section">
                    <h3>Smartphone et Objet Connecté</h3>
                    <section class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <?php if ($product['type_produit'] === 'telephonie'): ?>
                                <div class="product">
                                    <a href="detail_produit.php?id=<?= htmlspecialchars($product['IdProduit']) ?>&type=<?= htmlspecialchars($product['type_produit']) ?>">
                                        <img src="<?= htmlspecialchars($product['chemin_image'] ?: 'images/default.jpg') ?>" alt="<?= htmlspecialchars($product['Nom']) ?>">
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
            </div>
        </section>

    </main>
    
    <!-- section pour la partie aide  -->
    <section style="background-color: #2145e7;height: 150px;margin-top: 100px;">
        <div class="container" style="display: flex;justify-content: space-around;align-items: center;">
            <div class="text8" style="text-align: center;color: white;">
                <p style="font-size: 1.3rem;"><b>Vous avez besoin d'aide ? contactez le service client !</b></p>
                <p  style="font-size: 1.3rem;">support technique 24/7 au <b>+225 0142622547</b></p>
            </div>
            <div class="imput">
                <input type="email" name="mail" id="mail" placeholder="Entrez votre mail" style="width: 350px;height: 53px;">
                <button type="submit" class="button">envoyer</button>
            </div>
        </div>
    </section>
    <!-- pour le footer  -->
    <footer style="background-color: rgba(0, 0, 0, 0.91);">
        <div class="container" style="display: flex;justify-content: space-around;">
            <div class="footersous">
                <ul>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">A propos</a></li>
                    <li><a href="produit.php" style="text-decoration: none;list-style-type: none;color: white;">Produit</a></li>
                    <li><a href="index.php" style="text-decoration: none;list-style-type: none;color: white;">Acceuil</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Contact</a></li>
                </ul>
            </div>
            <div class="footersous">
                <ul>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Ordinateur Portable</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Ordinateur Sans Fil</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Tablettes</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Peripheriques</a></li>
                </ul>
            </div>
            <div class="footersous">
                <ul>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Personalisation Pc</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Personalisation Ordninateur Mobile</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Personalisation Peripherique</a></li>
                    <li><a href="#" style="text-decoration: none;list-style-type: none;color: white;">Personalisation Tablette</a></li>
                </ul>
            </div>
            <div class="footersous" style="background-color: #9d1cba;color: white;height: 200px;">
              <ul>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-geo-fill" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999zm2.493 8.574a.5.5 0 0 1-.411.575c-.712.118-1.28.295-1.655.493a1.3 1.3 0 0 0-.37.265.3.3 0 0 0-.057.09V14l.002.008.016.033a.6.6 0 0 0 .145.15c.165.13.435.27.813.395.751.25 1.82.414 3.024.414s2.273-.163 3.024-.414c.378-.126.648-.265.813-.395a.6.6 0 0 0 .146-.15l.015-.033L12 14v-.004a.3.3 0 0 0-.057-.09 1.3 1.3 0 0 0-.37-.264c-.376-.198-.943-.375-1.655-.493a.5.5 0 1 1 .164-.986c.77.127 1.452.328 1.957.594C12.5 13 13 13.4 13 14c0 .426-.26.752-.544.977-.29.228-.68.413-1.116.558-.878.293-2.059.465-3.34.465s-2.462-.172-3.34-.465c-.436-.145-.826-.33-1.116-.558C3.26 14.752 3 14.426 3 14c0-.599.5-1 .961-1.243.505-.266 1.187-.467 1.957-.594a.5.5 0 0 1 .575.411"/>
                    </svg> <a href="" style="text-decoration: none;list-style-type: none;color: white;">Rembals, Koumassi, Abidjan, Côte d'Ivoire</a></li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                    </svg><a href="" style="text-decoration: none;list-style-type: none;color: white;">+225 07 79 79 71 04</a></li>
                <li>
                  <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-envelope-at-fill" viewBox="0 0 16 16">
                      <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671"/>
                      <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791"/>
                    </svg><a href="" style="text-decoration: none;list-style-type: none;color: white;">infos@innovinvest.ci</a></li>
                <li><a href="" style="text-decoration: none;list-style-type: none;color: white;">
                  <!-- pour facebook  -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                      <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                    </svg>
                  <!-- whatssapp  -->
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                      <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                    </svg>
                 </a></li>
              </ul>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        function toggleAccountMenu() {
            const accountDropdown = document.querySelector('.account-dropdown');
            accountDropdown.classList.toggle('active'); // Toggle la classe 'active' pour afficher ou masquer
        }
        // Écouteur d'événement pour fermer le menu en cliquant en dehors de celui-ci
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.account-menu')) {
                const accountDropdowns = document.querySelectorAll('.account-dropdown');
                accountDropdowns.forEach(function(dropdown) {
                    dropdown.classList.remove('active');
                });
            }
        });
    </script>
</body>
</body>
</html>
