<?php
session_start();

// Inclusion du fichier de connexion à la base de données
require_once 'connexion_bdd.php';

if (!isset($_SESSION['client_id'])) {
    header('Location: ../connexion.php');
    exit;
}


try {
    // Récupération des informations de la session
    $client_id = $_SESSION['client_id'];
    $total = isset($_SESSION['total']) ? $_SESSION['total'] : 0;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    // Vérification que le montant total est valide
    if ($total <= 0) {
        throw new Exception("Le montant total de la commande est invalide.");
    }

    // Récupération des articles du panier depuis la session (remplacez cette partie avec votre propre logique)
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    // Récupération des informations du client depuis la base de données
    $sql_client = "SELECT * FROM client WHERE id = :id";
    $stmt_client = $pdo_utilisateur->prepare($sql_client);
    $stmt_client->execute(['id' => $client_id]);
    $client_info = $stmt_client->fetch();

    // Début de la transaction pour assurer l'intégrité des données
    $pdo_produit->beginTransaction();

    // Insertion des commandes dans la table `commandes`
    foreach ($cartItems as $item) {
        // Vérifier que les clés existent avant de les utiliser
        $produit_id = isset($item['IdProduit']) ? $item['IdProduit'] : null;
        $type_produit = isset($item['TypeProduit']) ? $item['TypeProduit'] : null;
        $quantite = isset($item['quantite']) ? $item['quantite'] : null;
        $prix_unitaire = isset($item['Prix']) ? $item['Prix'] : null;

        if ($produit_id !== null && $type_produit !== null && $quantite !== null && $prix_unitaire !== null) {
            $prix_total = $quantite * $prix_unitaire;
            $date_commande = date('Y-m-d H:i:s'); // Date et heure actuelle

            // Préparation de la requête d'insertion
            $sql_commande = "INSERT INTO commandes (client_id, nom_client, produit_id, type_produit, quantite, prix_unitaire, prix_total, date_commande)
                             VALUES (:client_id, :nom_client, :produit_id, :type_produit, :quantite, :prix_unitaire, :prix_total, :date_commande)";
            $stmt_commande = $pdo_produit->prepare($sql_commande);
            // Utilisation de bindValue pour les valeurs non modifiables directement
            $stmt_commande->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            $stmt_commande->bindValue(':nom_client', $client_info['nom'] . ' ' . $client_info['prenom'], PDO::PARAM_STR);
            $stmt_commande->bindValue(':produit_id', $produit_id, PDO::PARAM_INT);
            $stmt_commande->bindValue(':type_produit', $type_produit, PDO::PARAM_STR);
            $stmt_commande->bindValue(':quantite', $quantite, PDO::PARAM_INT);
            $stmt_commande->bindValue(':prix_unitaire', $prix_unitaire, PDO::PARAM_INT);
            $stmt_commande->bindValue(':prix_total', $prix_total, PDO::PARAM_INT);
            $stmt_commande->bindValue(':date_commande', $date_commande, PDO::PARAM_STR);
            $stmt_commande->execute();
        }
    }

    // Commit de la transaction si tout s'est bien passé
    $pdo_produit->commit();

    // Nettoyage de la session après la commande
    unset($_SESSION['cart']);
    unset($_SESSION['total']);

    // Redirection vers une page de confirmation de commande
    header('Location: confirmation.php');
    exit;

} catch (PDOException $e) {
    // En cas d'erreur PDO, annuler la transaction et afficher l'erreur
    $pdo_produit->rollBack();
    echo 'Erreur de connexion : ' . $e->getMessage();
    exit;
} catch (Exception $e) {
    // En cas d'erreur générale, afficher l'erreur
    echo 'Erreur : ' . $e->getMessage();
    exit;
}
?>
