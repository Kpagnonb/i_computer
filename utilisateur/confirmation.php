<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$client = $_SESSION['client_id'];

// Réinitialiser le panier après la commande
unset($_SESSION['cart']);
unset($_SESSION['total']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innov Invest - Confirmation</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header-logo">
            <img src="logo.png" alt="Logo" class="img-fluid">
        </header>

        <section class="confirmation-section">
            <h1>Merci pour votre commande</h1>
            <p>Votre commande a été enregistrée avec succès.</p>
            <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
        </section>
    </div>
</body>
</html>
