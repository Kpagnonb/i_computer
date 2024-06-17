-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 16 juin 2024 à 17:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `produit`
--

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `nom_client` varchar(255) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `type_produit` varchar(100) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` bigint(20) NOT NULL,
  `prix_total` bigint(20) NOT NULL,
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `chemin_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `images` (`id`, `id_produit`, `chemin_image`) VALUES
(4, 2, '../utilisateur/images/hp15-2.jpg'),
(5, 2, '../utilisateur/images/hp15-1.jpg'),
(6, 2, '../utilisateur/images/hp15-1.webp'),
(7, 3, '../utilisateur/images/lenovo15-3.jpg'),
(8, 3, '../utilisateur/images/lenovo15-2.jpg'),
(9, 3, '../utilisateur/images/lenovo15-1.jpg');


CREATE TABLE `images_peripheriques_reseaux` (
  `id` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `chemin_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `images_peripheriques_reseaux` (`id`, `id_produit`, `chemin_image`) VALUES
(4, 1, '../utilisateur/images/canon-1.webp');

CREATE TABLE `images_telephonie` (
  `id` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `chemin_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `images_telephonie`
--

INSERT INTO `images_telephonie` (`id`, `id_produit`, `chemin_image`) VALUES
(2, 2, '../utilisateur/images/air-2.jpg'),
(3, 2, '../utilisateur/images/air-2.webp'),
(4, 2, '../utilisateur/images/air-1.webp'),
(5, 4, '../utilisateur/images/tele-3.webp'),
(6, 4, '../utilisateur/images/tele-2.jpg'),
(7, 4, '../utilisateur/images/tele-1.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `ordinateurs`
--

CREATE TABLE `ordinateurs` (
  `IdProduit` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Marque` varchar(100) DEFAULT NULL,
  `Modele` varchar(100) DEFAULT NULL,
  `Processeur` varchar(100) DEFAULT NULL,
  `RAM` varchar(50) DEFAULT NULL,
  `Stockage` varchar(50) DEFAULT NULL,
  `GPU` varchar(100) DEFAULT NULL,
  `OS` varchar(100) DEFAULT NULL,
  `Prix` bigint(20) NOT NULL,
  `Description` text DEFAULT NULL,
  `DateAjout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ordinateurs`
--

INSERT INTO `ordinateurs` (`IdProduit`, `Nom`, `Marque`, `Modele`, `Processeur`, `RAM`, `Stockage`, `GPU`, `OS`, `Prix`, `Description`, `DateAjout`) VALUES
(2, 'HP 15-EG3013NF', 'HP', '15-EG3013NF', 'Core i5', '16 Go', 'SSD 480/512 Go', 'INTEL', 'Windows 11', 425716, 'Ce PC portable HP 15-eg3013nf est un outil de choix pour toutes vos tâches quotidiennes de bureautique. Conçu pour offrir une performance de haut niveau, il est équipé d\'un processeur Intel Core i5 et d\'une mémoire RAM de 16 Go pour une expérience fluide et rapide. Son écran de 15,6\" en résolution Full HD vous permettra de travailler en tout confort, avec une qualité d\'image optimale.\r\n\r\n\r\nDesign élégant et moderne en gris\r\n\r\n\r\nLe HP 15-eg3013nf se distingue également par son design élégant et moderne en gris, qui lui donne un aspect professionnel et raffiné. Avec sa taille de 15,6\", il est facile à transporter et s\'adapte parfaitement à votre espace de travail. Que ce soit pour un usage professionnel ou personnel, ce PC portable conviendra à tous vos besoins.\r\n\r\n\r\nPerformances graphiques optimales avec Intel intégré\r\n\r\n\r\nGrâce à son chipset graphique intégré Intel, le PC portable HP 15-eg3013nf offre des performances graphiques optimales pour toutes vos activités multimédias. Regardez vos vidéos en streaming, modifiez vos photos ou jouez à vos jeux préférés sans aucun ralentissement. Vous pourrez profiter de vos contenus en toute fluidité et avec une qualité d\'image époustouflante.', '2024-06-15 16:14:03'),
(3, 'LENOVO V15 G4 ', 'LENOVO', 'V15 G4 ', 'AMD Ryzen 3', '8 Go', 'SSD 240/256 Go', 'AMD', 'Windows 11', 248000, 'Performance Maximale avec le Lenovo V15 G4 AMN\r\n\r\n\r\nLe Lenovo V15 G4 AMN est équipé du processeur AMD Ryzen 3 7320U offrant une fréquence pouvant atteindre 4,1 GHz pour une rapidité et une fluidité de fonctionnement exceptionnelles. Avec une mémoire RAM LPDDR5 de 8 Go et un stockage SSD NVMe de 256 Go, il assure des performances optimales pour toutes vos tâches. Pour la sécurité de vos informations, le chip de sécurité Firmware Trusted Platform Module (TPM 2.0) est intégré.\r\n\r\n\r\nQualité d\'Affichage Époustouflante\r\n\r\n\r\nLe Lenovo V15 G4 AMN dispose d\'un écran de 15,6 pouces en Full HD, offrant une résolution de 1920 x 1080 pixels pour des images détaillées et des couleurs éclatantes. Grâce à la carte graphique AMD Radeon 610M, vivez une expérience visuelle hors du commun. Et avec une autonomie de batterie pouvant atteindre 14,4 heures, vous bénéficiez d\'une utilisation prolongée.', '2024-06-15 16:16:44');

-- --------------------------------------------------------

--
-- Structure de la table `peripheriques_reseaux`
--

CREATE TABLE `peripheriques_reseaux` (
  `IdProduit` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Prix` bigint(20) NOT NULL,
  `Marque` varchar(100) DEFAULT NULL,
  `TypeProduit` enum('Écran PC','Clavier','Imprimante','Webcam','Disque dur externe','Périphérique gaming','Solution réseau','Cartouche/toner') NOT NULL,
  `DateAjout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `peripheriques_reseaux`
--

INSERT INTO `peripheriques_reseaux` (`IdProduit`, `Nom`, `Description`, `Prix`, `Marque`, `TypeProduit`, `DateAjout`) VALUES
(1, 'CANON PIXMA TS705A', 'Canon PIXMA TS705a\r\nCanon PIXMA TS705a. Couleur, Nombre de cartouches d\'impression: 5. Résolution maximale: 4800 x 1200 DPI. Taille de papier de série A ISO maximum: A4. Vitesse d\'impression (noir, qualité normale, A4/US Letter): 15 ppm. Impression recto-verso. Écran: LCD. Wifi. Couleur du produit: Noir', 43949, 'PIXMA TS705A', 'Imprimante', '2024-06-15 14:40:54');

-- --------------------------------------------------------

--
-- Structure de la table `telephonie`
--

CREATE TABLE `telephonie` (
  `IdProduit` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Prix` bigint(20) NOT NULL,
  `Marque` varchar(100) DEFAULT NULL,
  `TypeProduit` enum('Smartphone','Accessoire téléphone','Objet connecté','Téléphone fixe') NOT NULL,
  `DateAjout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `telephonie`
--

INSERT INTO `telephonie` (`IdProduit`, `Nom`, `Description`, `Prix`, `Marque`, `TypeProduit`, `DateAjout`) VALUES
(2, 'APPLE AIRPODS PRO', 'Les Airpods Pro de 2ème génération offrent à la fois le style et la performance de qualité Apple. Ces Airpods Pro comprennent des fonctionnalités de pointe telles que la suppression du bruit active, un ajustement personnalisé et une connexion sans fil à n&amp;#039;importe quel appareil Apple pour une expérience d&amp;#039;écoute transparente et ininterrompue. Disponible avec un boîtier de charge pour que vous puissiez toujours être prêt à partir, les Airpods Pro Apple sont un must pour les amateurs de technologie.\r\n\r\n\r\nDurabilité et Confort\r\n\r\n\r\nLes Airpods Pro Apple ont été conçus pour un confort et une durabilité à toute épreuve. Fabriqués à partir d&amp;#039;un matériau antidérapant, ils offrent une prise en main fiable et une sensation qui ne quittera jamais vos oreilles. Avec des coussinets en silicone ultra-doux qui s&amp;#039;adaptent à la forme de votre conduit auditif, vous pouvez écouter en toute confiance pendant des heures.\r\n\r\n\r\nRéduction du bruit et ajustement personnalisé\r\n\r\n\r\nLes Airpods Pro offrent une suppression du bruit active puissante et réglable, ce qui signifie que vous pouvez ajuster le niveau de réduction du bruit pour obtenir une isolation optimale. Avec un ajustement personnalisé réglable qui s&amp;#039;adapte à la forme de votre oreille, vous obtenez l&amp;#039;efficacité et la clarté audio dont vous avez besoin. De plus, les AirPods Pro fonctionnent exactement comme les Airpods classiques, ce qui signifie qu&amp;#039;ils sont parfaits pour les appels téléphoniques, la musique et le streaming.', 183012, 'APPLE', 'Accessoire téléphone', '2024-06-15 15:53:19'),
(4, 'STRONG SRT 50UD7553', 'Si vous recherchez un téléviseur à la fois robuste et innovant, la télévision Strong SRT 50UD7553 de 50\" est la solution parfaite. Offrant une qualité d\'image 4K UHD sur une dalle LED, cette télévision vous offrira des couleurs éclatantes et des images nettes et vives. Avec une taille de 50\" (127cm), la télévision Strong SRT 50UD7553 vous offrira un angle de vue plus large pour vous immerger encore plus dans vos séries préférées ou vos films cultes. Enfin, avec sa fonction SMART TV, vous pourrez profiter d\'un monde de divertissement infini avec l\'accès à de nombreuses chaînes et applications.\r\n\r\n\r\nTaille :\r\n\r\n\r\nLa télévision Strong SRT 50UD7553 est une télévision LED UHD 4K de 50\" (127cm). Avec une taille aussi grande, vous pourrez bénéficier d\'une qualité d\'image plus nette et d\'un angle de vision plus large lorsque vous regardez vos programmes préférés. Grâce à sa taille, cette télévision conviendra parfaitement aux petits et grands espaces, offrant une image digne des plus grands salles de cinéma.\r\n\r\n\r\nRésolution :\r\n\r\n\r\nLa télévision Strong SRT 50UD7553 offre une qualité d\'image 4K UHD à 3860 x 2160. Cela signifie que vous pourrez bénéficier des meilleurs détails et couleurs de l\'image, vous offrant une image digne des salles de cinéma. Les films et séries ne vous paraîtront plus les mêmes, d\'autant plus que la dalle LED offre des couleurs plus éclatantes et des contrastes plus profonds pour chaque image.\r\n\r\n\r\nType de Dalle LED UHD SMART TV:\r\n\r\n\r\nEn plus de sa qualité d\'image 4K UHD, cette télévision dispose également d\'une dalle LED UHD pour des couleurs plus éclatantes et des contrastes plus profonds pour chaque image. Enfin, grâce à sa fonction SMART TV, vous pourrez profiter d\'un monde de divertissement infini, avec l\'accès à de nombreuses chaînes et applications.', 228928, 'STRONG', 'Objet connecté', '2024-06-15 16:10:24');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_images_produit` (`id_produit`);

--
-- Index pour la table `images_peripheriques_reseaux`
--
ALTER TABLE `images_peripheriques_reseaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_images_peri_produit` (`id_produit`);

--
-- Index pour la table `images_telephonie`
--
ALTER TABLE `images_telephonie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_images_tel_produit` (`id_produit`);

--
-- Index pour la table `ordinateurs`
--
ALTER TABLE `ordinateurs`
  ADD PRIMARY KEY (`IdProduit`);

--
-- Index pour la table `peripheriques_reseaux`
--
ALTER TABLE `peripheriques_reseaux`
  ADD PRIMARY KEY (`IdProduit`);

--
-- Index pour la table `telephonie`
--
ALTER TABLE `telephonie`
  ADD PRIMARY KEY (`IdProduit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `images_peripheriques_reseaux`
--
ALTER TABLE `images_peripheriques_reseaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `images_telephonie`
--
ALTER TABLE `images_telephonie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `ordinateurs`
--
ALTER TABLE `ordinateurs`
  MODIFY `IdProduit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `peripheriques_reseaux`
--
ALTER TABLE `peripheriques_reseaux`
  MODIFY `IdProduit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `telephonie`
--
ALTER TABLE `telephonie`
  MODIFY `IdProduit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_produit` FOREIGN KEY (`id_produit`) REFERENCES `ordinateurs` (`IdProduit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `images_peripheriques_reseaux`
--
ALTER TABLE `images_peripheriques_reseaux`
  ADD CONSTRAINT `fk_images_peri_produit` FOREIGN KEY (`id_produit`) REFERENCES `peripheriques_reseaux` (`IdProduit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `images_telephonie`
--
ALTER TABLE `images_telephonie`
  ADD CONSTRAINT `fk_images_tel_produit` FOREIGN KEY (`id_produit`) REFERENCES `telephonie` (`IdProduit`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
