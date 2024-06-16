<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: connexion.php');
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

// Message de succès de la session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Supprimer le message après l'avoir affiché une fois

try {
    // Connexion à la base de données
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des détails de l'ordinateur à modifier
    $sql_select_produit = "SELECT * FROM ordinateurs WHERE IdProduit = :id_produit";
    $stmt_select_produit = $pdo->prepare($sql_select_produit);
    $stmt_select_produit->execute(['id_produit' => $id_produit]);
    $produit = $stmt_select_produit->fetch(PDO::FETCH_ASSOC);

    // Récupération des images associées à l'ordinateur
    $sql_select_images = "SELECT * FROM images WHERE id_produit = :id_produit";
    $stmt_select_images = $pdo->prepare($sql_select_images);
    $stmt_select_images->execute(['id_produit' => $id_produit]);
    $images = $stmt_select_images->fetchAll(PDO::FETCH_ASSOC);

    // Traitement du formulaire lorsqu'il est soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom'];
        $marque = $_POST['marque'];
        $modele = $_POST['modele'];
        $processeur = $_POST['processeur'];
        $ram = $_POST['ram'];
        $stockage = $_POST['stockage'];
        $gpu = $_POST['gpu'];
        $os = $_POST['os'];
        $prix = $_POST['prix'];
        $description = $_POST['description'];

        // Mise à jour des détails de l'ordinateur
        $sql_update_produit = "UPDATE ordinateurs SET 
                               Nom = :nom, 
                               Marque = :marque, 
                               Modele = :modele, 
                               Processeur = :processeur, 
                               RAM = :ram, 
                               Stockage = :stockage, 
                               GPU = :gpu, 
                               OS = :os, 
                               Prix = :prix, 
                               Description = :description 
                               WHERE IdProduit = :id_produit";
        $stmt_update_produit = $pdo->prepare($sql_update_produit);
        $stmt_update_produit->execute([
            'nom' => $nom,
            'marque' => $marque,
            'modele' => $modele,
            'processeur' => $processeur,
            'ram' => $ram,
            'stockage' => $stockage,
            'gpu' => $gpu,
            'os' => $os,
            'prix' => $prix,
            'description' => $description,
            'id_produit' => $id_produit
        ]);

        // Gestion des images
        $uploadDir = '../utilisateur/images/';
        
        // Si des images sont téléchargées, les traiter
        if (!empty($_FILES['images']['tmp_name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmp_name, $filePath)) {
                    // Insertion du chemin de l'image dans la base de données
                    $sql_insert_image = "INSERT INTO images (id_produit, chemin_image) VALUES (:id_produit, :chemin_image)";
                    $stmt_insert_image = $pdo->prepare($sql_insert_image);
                    $stmt_insert_image->execute(['id_produit' => $id_produit, 'chemin_image' => $filePath]);
                } else {
                    echo "Erreur lors du téléchargement de l'image: " . $fileName;
                }
            }
        }

        // Suppression des images sélectionnées
        if (isset($_POST['images_a_supprimer'])) {
            $images_a_supprimer = $_POST['images_a_supprimer'];

            foreach ($images_a_supprimer as $image_id) {
                // Suppression de l'image du serveur
                $sql_delete_image = "SELECT chemin_image FROM images WHERE id = :image_id";
                $stmt_delete_image = $pdo->prepare($sql_delete_image);
                $stmt_delete_image->execute(['image_id' => $image_id]);
                $chemin_image = $stmt_delete_image->fetch(PDO::FETCH_ASSOC)['chemin_image'];

                if ($chemin_image && file_exists($chemin_image)) {
                    unlink($chemin_image);
                }

                // Suppression de l'enregistrement de l'image dans la base de données
                $sql_delete_image_record = "DELETE FROM images WHERE id = :image_id";
                $stmt_delete_image_record = $pdo->prepare($sql_delete_image_record);
                $stmt_delete_image_record->execute(['image_id' => $image_id]);
            }
        }

        // Redirection avec un message de succès
        $_SESSION['message'] = 'Les détails de l\'ordinateur ont été mis à jour avec succès!';
        header('Location: ordinateur.php');
        exit;
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
            <li class="nav-item active" >
                <a class="nav-link collapsed" href="ordinateur.php" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-desktop"></i>
                    <span>Ordinateurs</span></a>
            </li>
            <li class="nav-item">
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
                <section class="container mt-5">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <!-- Message de succès -->
                            <?php if (!empty($message)) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?= $message ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Formulaire de modification -->
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="nom">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($produit['Nom']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="marque">Marque</label>
                                    <input type="text" class="form-control" id="marque" name="marque" value="<?= htmlspecialchars($produit['Marque']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="modele">Modèle</label>
                                    <input type="text" class="form-control" id="modele" name="modele" value="<?= htmlspecialchars($produit['Modele']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="processeur">Processeur</label>
                                    <input type="text" class="form-control" id="processeur" name="processeur" value="<?= htmlspecialchars($produit['Processeur']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="ram">RAM</label>
                                    <input type="text" class="form-control" id="ram" name="ram" value="<?= htmlspecialchars($produit['RAM']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="stockage">Stockage</label>
                                    <input type="text" class="form-control" id="stockage" name="stockage" value="<?= htmlspecialchars($produit['Stockage']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="gpu">Carte graphique</label>
                                    <input type="text" class="form-control" id="gpu" name="gpu" value="<?= htmlspecialchars($produit['GPU']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="os">Système d'exploitation</label>
                                    <input type="text" class="form-control" id="os" name="os" value="<?= htmlspecialchars($produit['OS']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="prix">Prix</label>
                                    <input type="text" class="form-control" id="prix" name="prix" value="<?= htmlspecialchars($produit['Prix']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($produit['Description']) ?></textarea>
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
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </form>
                        </div>
                    </div>
                </section>


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