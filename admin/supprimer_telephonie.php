<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin'])) {
    header('Location: ../connexion.php');
    exit;
}

$admin = $_SESSION['admin'];

// Vérifier la présence de l'ID du telephonie à supprimer dans l'URL
if (!isset($_GET['id'])) {
    header('Location: telephonie.php'); // Redirection si l'ID est absent
    exit;
}

$id_produit = $_GET['id'];

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "produit";

// Connexion à la base de données MySQL avec l'API MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    // Suppression des images associées au telephonie réseau
    $sql_select_images = "SELECT chemin_image FROM images_telephonie WHERE id_produit = ?";
    $stmt_select_images = $conn->prepare($sql_select_images);
    $stmt_select_images->bind_param('i', $id_produit);
    $stmt_select_images->execute();
    $result_images = $stmt_select_images->get_result();
    
    // Parcourir et supprimer chaque image du serveur
    while ($row = $result_images->fetch_assoc()) {
        $filePath = $row['chemin_image'];
        if (file_exists($filePath)) {
            unlink($filePath); // Suppression du fichier image du serveur
        }
    }

    // Suppression des enregistrements d'images dans la table images_telephonie
    $sql_delete_images = "DELETE FROM images_telephonie WHERE id_produit = ?";
    $stmt_delete_images = $conn->prepare($sql_delete_images);
    $stmt_delete_images->bind_param('i', $id_produit);
    $stmt_delete_images->execute();

    // Suppression du telephonie  dans la table telephonie
    $sql_delete_peripherique = "DELETE FROM telephonie WHERE IdProduit = ?";
    $stmt_delete_peripherique = $conn->prepare($sql_delete_peripherique);
    $stmt_delete_peripherique->bind_param('i', $id_produit);
    $stmt_delete_peripherique->execute();

    // Redirection avec un message de succès
    $_SESSION['message'] = 'La telephonie  a été supprimé avec succès!';
    header('Location: telephonie.php');
    exit;
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
}

// Fermer la connexion
$conn->close();
?>
