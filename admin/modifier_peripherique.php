<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../connexion.php');
    exit;
}

$admin = $_SESSION['admin'];

// Vérification de l'existence du paramètre id dans l'URL
if (!isset($_GET['id'])) {
    header('Location: peripheriques.php'); // Redirection en cas de paramètre manquant
    exit;
}

$id_produit = $_GET['id'];

// Paramètres de connexion PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "produit";

// Connexion PDO
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Gestion des erreurs
    PDO::ATTR_EMULATE_PREPARES => false, // Désactivation de l'émulation des requêtes préparées
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des détails du périphérique à modifier
$sql_select_produit = "SELECT * FROM peripheriques_reseaux WHERE IdProduit = :id_produit";
$stmt_select_produit = $pdo->prepare($sql_select_produit);
$stmt_select_produit->execute(['id_produit' => $id_produit]);
$produit = $stmt_select_produit->fetch(PDO::FETCH_ASSOC);

// Récupération des images associées au périphérique
$sql_select_images = "SELECT id, chemin_image FROM images_peripheriques_reseaux WHERE id_produit = :id_produit";
$stmt_select_images = $pdo->prepare($sql_select_images);
$stmt_select_images->execute(['id_produit' => $id_produit]);
$images = $stmt_select_images->fetchAll(PDO::FETCH_ASSOC);

// Message de succès de la session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Supprimer le message après l'avoir affiché une fois

try {
    // Traitement du formulaire lorsqu'il est soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $marque = $_POST['marque'];
        $typeProduit = $_POST['typeProduit'];
        $images_a_supprimer = isset($_POST['images_a_supprimer']) ? $_POST['images_a_supprimer'] : [];

        // Gestion de l'upload de fichier
        $imagePaths = [];
        if (isset($_FILES['images'])) {
            $uploadDir = "../utilisateur/images/";
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $target_file = $uploadDir . basename($_FILES['images']['name'][$key]);
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $imagePaths[] = $target_file;
                }
            }
        }

        // Requête préparée pour l'update
        $sql_update_produit = "UPDATE peripheriques_reseaux SET 
                               Nom = :nom, 
                               Description = :description, 
                               Prix = :prix, 
                               Marque = :marque, 
                               TypeProduit = :typeProduit, 
                               DateAjout = NOW()
                               WHERE IdProduit = :id_produit";
        $stmt_update_produit = $pdo->prepare($sql_update_produit);
        $stmt_update_produit->execute([
            'nom' => $nom,
            'description' => $description,
            'prix' => $prix,
            'marque' => $marque,
            'typeProduit' => $typeProduit,
            'id_produit' => $id_produit
        ]);

        // Si la mise à jour s'est bien passée, gérer les images
        if ($stmt_update_produit->rowCount() > 0) {
            // Supprimer les images sélectionnées
            foreach ($images_a_supprimer as $image_id) {
                $sql_delete_image = "DELETE FROM images_peripheriques_reseaux WHERE id = :image_id";
                $stmt_delete_image = $pdo->prepare($sql_delete_image);
                $stmt_delete_image->execute(['image_id' => $image_id]);
            }

            // Insérer de nouvelles images
            foreach ($imagePaths as $imagePath) {
                $sql_insert_image = "INSERT INTO images_peripheriques_reseaux (id_produit, chemin_image) VALUES (:id_produit, :image_path)";
                $stmt_insert_image = $pdo->prepare($sql_insert_image);
                $stmt_insert_image->execute([
                    'id_produit' => $id_produit,
                    'image_path' => $imagePath
                ]);
            }

            $_SESSION['message'] = 'Les détails du périphérique ont été mis à jour avec succès!';
            header('Location: peripherique.php');
            exit;
        } else {
            echo "Erreur lors de la mise à jour du périphérique.";
        }
    }
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Administrateur</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="path/to/bootstrap.min.css" rel="stylesheet">
    <link href="path/to/fontawesome.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_admin.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Direction Technqiue</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="dashboard_admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item " >
                <a class="nav-link collapsed" href="ordinateur.php" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-desktop"></i>
                    <span>Ordinateurs</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link collapsed" href="peripherique.php"  data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-network-wired"></i>
                    <span>Périphériques & Réseaux</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="telephonie.php"  data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-mobile-alt"></i>
                    <span>Téléphonie & Objets Connectés</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($admin['nom']) ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <form action="../deconnexion.php" method="post" style="margin: 0;">
                                    <button class="dropdown-item" type="submit">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <!-- Main Content -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Modifier un périphérique réseau</h1>
                    <form action="modifier_peripherique.php?id=<?php echo $id_produit; ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($produit['Nom']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($produit['Description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="prix">Prix</label>
                            <input type="number" class="form-control" id="prix" name="prix" value="<?php echo htmlspecialchars($produit['Prix']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="marque">Marque</label>
                            <input type="text" class="form-control" id="marque" name="marque" value="<?php echo htmlspecialchars($produit['Marque']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="typeProduit">Type de Produit</label>
                            <select class="form-control" id="typeProduit" name="typeProduit">
                                <option value="Écran PC" <?php if ($produit['TypeProduit'] == 'Écran PC') echo 'selected'; ?>>Écran PC</option>
                                <option value="Clavier" <?php if ($produit['TypeProduit'] == 'Clavier') echo 'selected'; ?>>Clavier</option>
                                <option value="Imprimante" <?php if ($produit['TypeProduit'] == 'Imprimante') echo 'selected'; ?>>Imprimante</option>
                                <option value="Webcam" <?php if ($produit['TypeProduit'] == 'Webcam') echo 'selected'; ?>>Webcam</option>
                                <option value="Disque dur externe" <?php if ($produit['TypeProduit'] == 'Disque dur externe') echo 'selected'; ?>>Disque dur externe</option>
                                <option value="Périphérique gaming" <?php if ($produit['TypeProduit'] == 'Périphérique gaming') echo 'selected'; ?>>Périphérique gaming</option>
                                <option value="Solution réseau" <?php if ($produit['TypeProduit'] == 'Solution réseau') echo 'selected'; ?>>Solution réseau</option>
                                <option value="Cartouche/toner" <?php if ($produit['TypeProduit'] == 'Cartouche/toner') echo 'selected'; ?>>Cartouche/toner</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="images">Images</label>
                            <input type="file" class="form-control-file" id="images" name="images[]" multiple>
                        </div>
                        <div class="form-group">
                            <label>Images actuelles</label>
                            <div class="row">
                                <?php foreach ($images as $image) : ?>
                                    <div class="col-md-3 mb-2">
                                        <img src="<?= htmlspecialchars($image['chemin_image']) ?>" class="img-fluid" alt="Image produit">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" value="<?= $image['id'] ?>" name="images_a_supprimer[]">
                                            <label class="form-check-label">Supprimer</label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Modifier</button>
                    </form>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Bootstrap core JavaScript-->
    <script src="path/to/jquery.min.js"></script>
    <script src="path/to/bootstrap.bundle.min.js"></script>
    <script src="path/to/fontawesome.min.js"></script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>