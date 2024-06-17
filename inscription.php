<?php
session_start();

// Configuration de la base de données
$dsn = 'mysql:host=localhost;dbname=utilisateur;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = $_POST['titre'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $ville = $_POST['ville'];
        $code_postal = $_POST['code_postal'];
        $email = $_POST['email'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
        $telephone = $_POST['telephone'];

        // Préparation et exécution de la requête d'insertion
        $sql = "INSERT INTO client (titre, nom, prenom, adresse, ville, code_postal, email, mot_de_passe, telephone) 
                VALUES (:titre, :nom, :prenom, :adresse, :ville, :code_postal, :email, :mot_de_passe, :telephone)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titre' => $titre,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':adresse' => $adresse,
            ':ville' => $ville,
            ':code_postal' => $code_postal,
            ':email' => $email,
            ':mot_de_passe' => $mot_de_passe,
            ':telephone' => $telephone
        ]);

        // Récupération de l'ID du client nouvellement inscrit
        $client_id = $pdo->lastInsertId();

        // Mise à jour de la session avec l'ID du client
        $_SESSION['client_id'] = $client_id;

        // Redirection vers la page d'accueil du client (index.php dans ce cas)
        $_SESSION['success'] = "Inscription réussie !";
        header('Location: utilisateur/index.php');
        exit;
    }
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Inscription</h1>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <form action="inscription.php" method="POST">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <div>
                <input type="radio" id="mr" name="titre" value="Mr" required>
                <label for="mr">Mr</label>
                <input type="radio" id="mme" name="titre" value="Mme">
                <label for="mme">Mme</label>
                <input type="radio" id="mlle" name="titre" value="Mlle">
                <label for="mlle">Mlle</label>
            </div>
        </div>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" required>
        </div>
        <div class="mb-3">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" class="form-control" id="ville" name="ville" required>
        </div>
        <div class="mb-3">
            <label for="code_postal" class="form-label">Code Postal</label>
            <input type="text" class="form-control" id="code_postal" name="code_postal" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="tel" class="form-control" id="telephone" name="telephone" required>
        </div>
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>
</body>
</html>
