<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    // Redirection vers la page de connexion ou affichage d'un message d'erreur
    header('Location: connexion.php');
    exit;
}

// Inclusion de votre fichier de connexion à la base de données
require_once 'connexion_bdd.php';

try {
    $client_id = $_SESSION['client_id'];

    // Récupérer les informations du client depuis la base de données utilisateur
    $sql_client = "SELECT * FROM client WHERE id = :id";
    $stmt_client = $pdo_utilisateur->prepare($sql_client);
    $stmt_client->execute(['id' => $client_id]);
    $client_info = $stmt_client->fetch();

    if (!$client_info) {
        // Gérer le cas où aucun client n'est trouvé pour cet ID
        throw new Exception("Aucune information client trouvée pour l'utilisateur connecté.");
    }

    // Récupérer les articles du panier depuis la session (remplacez cette partie avec votre propre logique)
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    // Calcul du total (remplacez cette partie avec votre propre logique)
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['Prix'] * $item['quantite'];
    }

    $_SESSION['total'] = $total;

} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innov Invest - Paiement</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/paiement.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header-logo">
            <img src="logo.png" alt="Logo" class="img-fluid">
        </header>

        <section class="paiement-section">
            <h1>Paiement</h1>
            <div class="client-info">
                <h3>Informations du client</h3>
                <?php if (!empty($client_info)) : ?>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($client_info['nom'] . ' ' . $client_info['prenom']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($client_info['email']) ?></p>
                    <p><strong>Adresse de livraison :</strong> <?= htmlspecialchars($client_info['adresse']) ?>, <?= htmlspecialchars($client_info['ville']) ?>, <?= htmlspecialchars($client_info['code_postal']) ?></p>
                <?php else : ?>
                    <p>Aucune information client trouvée.</p>
                <?php endif; ?>
            </div>
            <div class="cart-items mt-4">
                <h3>Votre commande</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['Nom']) ?></td>
                                <td><?= htmlspecialchars($item['quantite']) ?></td>
                                <td><?= htmlspecialchars($item['Prix']) ?> FCFA</td>
                                <td><?= htmlspecialchars($item['Prix'] * $item['quantite']) ?> FCFA</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total :</strong></td>
                            <td><?= htmlspecialchars($total) ?> FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="payment-methods mt-4">
                <h3>Méthodes de paiement</h3>
                <form action="process_paiement.php" method="post">
                    <div class="mb-3">
                        <label for="payment-method" class="form-label">Choisissez votre méthode de paiement :</label>
                        <select id="payment-method" name="payment_method" class="form-select" required>
                            <option value="credit_card">Carte de crédit</option>
                            <option value="paypal">PayPal</option>
                            <option value="paypal">Livraison</option>
                            <option value="bank_transfer">Virement bancaire</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Payer <?= htmlspecialchars($total) ?> FCFA</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
