<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../connexion.php');
    exit;
}

$admin = $_SESSION['admin'];

// Vérification de l'existence du paramètre id dans l'URL
if (!isset($_GET['id'])) {
    header('Location: ordinateur.php'); // Redirection en cas de paramètre manquant
    exit;
}

$id_produit = $_GET['id'];

// Paramètres de connexion PDO
$dsn = 'mysql:host=localhost;dbname=produit;charset=utf8';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression des images associées à l'ordinateur
    $sql_select_images = "SELECT chemin_image FROM images WHERE id_produit = :id_produit";
    $stmt_select_images = $pdo->prepare($sql_select_images);
    $stmt_select_images->execute(['id_produit' => $id_produit]);
    $images = $stmt_select_images->fetchAll(PDO::FETCH_ASSOC);

    foreach ($images as $image) {
        $filePath = $image['chemin_image'];
        if (file_exists($filePath)) {
            unlink($filePath); // Suppression du fichier image du serveur
        }
    }

    // Suppression des enregistrements d'images dans la base de données
    $sql_delete_images = "DELETE FROM images WHERE id_produit = :id_produit";
    $stmt_delete_images = $pdo->prepare($sql_delete_images);
    $stmt_delete_images->execute(['id_produit' => $id_produit]);

    // Suppression de l'ordinateur
    $sql_delete_produit = "DELETE FROM ordinateurs WHERE IdProduit = :id_produit";
    $stmt_delete_produit = $pdo->prepare($sql_delete_produit);
    $stmt_delete_produit->execute(['id_produit' => $id_produit]);

    // Redirection vers la liste des ordinateurs avec un message de succès
    $_SESSION['message'] = 'L\'ordinateur a été supprimé avec succès!';
    header('Location: ordinateur.php');
    exit;
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>
