-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 29 nov. 2024 à 10:26
-- Version du serveur : 9.1.0
-- Version de PHP : 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `superherobdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20241127125329', '2024-11-27 12:53:34', 661),
('DoctrineMigrations\\Version20241127135521', '2024-11-27 13:55:38', 149),
('DoctrineMigrations\\Version20241127141730', '2024-11-27 14:17:35', 56),
('DoctrineMigrations\\Version20241127142609', '2024-11-27 14:26:14', 72),
('DoctrineMigrations\\Version20241127175519', '2024-11-27 17:55:25', 111),
('DoctrineMigrations\\Version20241128054144', '2024-11-28 05:41:49', 16),
('DoctrineMigrations\\Version20241128074439', '2024-11-28 07:44:43', 37),
('DoctrineMigrations\\Version20241129102042', '2024-11-29 10:21:06', 117);

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

DROP TABLE IF EXISTS `equipe`;
CREATE TABLE IF NOT EXISTS `equipe` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chef_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_active` tinyint(1) NOT NULL,
  `cree_le` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_2449BA15150A48F1` (`chef_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id`, `chef_id`, `nom`, `est_active`, `cree_le`) VALUES
(24, 69, 'Les Gardiens Lumineux', 1, '2024-11-29 09:33:52'),
(25, 67, 'L\'Ordre des Ombres', 0, '2024-11-29 09:35:35'),
(26, 72, 'Les Titans de l\'Avenir', 1, '2024-11-29 09:36:26');

-- --------------------------------------------------------

--
-- Structure de la table `equipe_super_hero`
--

DROP TABLE IF EXISTS `equipe_super_hero`;
CREATE TABLE IF NOT EXISTS `equipe_super_hero` (
  `equipe_id` int NOT NULL,
  `super_hero_id` int NOT NULL,
  PRIMARY KEY (`equipe_id`,`super_hero_id`),
  KEY `IDX_970E3D1E6D861B89` (`equipe_id`),
  KEY `IDX_970E3D1EB62BE361` (`super_hero_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipe_super_hero`
--

INSERT INTO `equipe_super_hero` (`equipe_id`, `super_hero_id`) VALUES
(24, 70),
(24, 71),
(25, 68),
(25, 74),
(26, 73),
(26, 75);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mission`
--

DROP TABLE IF EXISTS `mission`;
CREATE TABLE IF NOT EXISTS `mission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `equipe_assignee_id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `date_fin` datetime NOT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau_danger` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9067F23C937C8CB0` (`equipe_assignee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mission`
--

INSERT INTO `mission` (`id`, `equipe_assignee_id`, `titre`, `description`, `statut`, `date_debut`, `date_fin`, `lieu`, `niveau_danger`) VALUES
(39, 26, 'Sauvetage dans la Tempête', 'Une tempête dévastatrice a frappé une grande ville, laissant des centaines de civils piégés sous les décombres. Votre mission est de sauver le plus de vies possible avant qu\'une seconde vague de la tempête n\'arrive.', 'FINIE', '2024-11-29 09:42:57', '2024-11-29 09:44:57', 'Métropole de Nova City', 4),
(40, 25, 'Protéger le Diplomate', 'Un diplomate de renom a reçu des menaces de mort lors d\'une visite à Gotham. Vous devez assurer sa sécurité et neutraliser toute menace.', 'ANNULÉE', '2024-11-29 11:44:00', '2024-11-29 11:46:00', 'Gotham City', 3),
(41, 24, 'La Bataille pour la Terre', 'Une flotte d\'envahisseurs extraterrestres attaque la planète. Votre mission est de protéger les civils et repousser les envahisseurs.', 'ANNULÉE', '2024-11-30 09:45:00', '2024-11-30 09:47:00', 'Tout le Globe', 5),
(45, 26, 'Virus Mortel', 'Un laboratoire secret a accidentellement libéré un virus qui transforme les gens en monstres. Vous devez contenir l\'épidémie et trouver un remède.', 'FINIE', '2024-11-29 10:21:33', '2024-11-29 10:23:33', 'Laboratoire d\'Avalon', 4),
(46, 25, 'Protéger un dignitaire en visite', 'Un dignitaire étranger est menacé par un groupe terroriste. Assurez sa protection tout au long de son séjour.', 'EN ATTENTE', '2024-11-29 13:22:00', '2024-11-29 13:24:00', 'Capitale de Rivertown', 3),
(47, 26, 'Stopper un rituel interdit', 'Une secte mystique tente de libérer une ancienne entité. Intervenez avant que le rituel ne soit terminé.', 'ÉCHOUÉE', '2024-11-29 10:24:36', '2024-11-29 10:26:36', 'Temple abandonné', 3);

-- --------------------------------------------------------

--
-- Structure de la table `mission_pouvoir`
--

DROP TABLE IF EXISTS `mission_pouvoir`;
CREATE TABLE IF NOT EXISTS `mission_pouvoir` (
  `mission_id` int NOT NULL,
  `pouvoir_id` int NOT NULL,
  PRIMARY KEY (`mission_id`,`pouvoir_id`),
  KEY `IDX_2F43FA8BBE6CAE90` (`mission_id`),
  KEY `IDX_2F43FA8BC8A705F8` (`pouvoir_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mission_pouvoir`
--

INSERT INTO `mission_pouvoir` (`mission_id`, `pouvoir_id`) VALUES
(39, 11),
(40, 1),
(41, 5),
(45, 1),
(46, 1),
(47, 9);

-- --------------------------------------------------------

--
-- Structure de la table `pouvoir`
--

DROP TABLE IF EXISTS `pouvoir`;
CREATE TABLE IF NOT EXISTS `pouvoir` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `pouvoir`
--

INSERT INTO `pouvoir` (`id`, `nom`, `description`, `niveau`) VALUES
(1, 'Super Force', 'Une force physique surhumaine capable de soulever des charges colossales.', 5),
(5, 'Téléportation', 'Permet de se déplacer instantanément d’un endroit à un autre.', 4),
(6, 'Vision Laser', 'Permet de tirer des rayons laser depuis les yeux pour couper ou brûler des obstacles.', 5),
(7, 'Invisibilité', 'Permet de devenir invisible à volonté pour se faufiler ou échapper à l’ennemi.', 3),
(8, 'Contrôle des Éléments', 'Maîtrise des forces de la nature comme le feu, l’eau, la terre, et l’air.', 5),
(9, 'Régénération Accélérée', 'Les coupures, fractures, brûlures ou empoisonnements ne durent jamais plus de quelques secondes. Même des blessures potentiellement mortelles, comme des tirs ou explosions, peuvent être surmontées.', 5),
(10, 'Vol', 'Permet de voler à des vitesses incroyables.', 4),
(11, 'Vitesse Supersonique', 'Se déplacer plus vite que la lumière.', 5),
(12, 'Bouclier d’énergie photonique', 'Pouvoir de protection, Solidifier la lumière autour d’elle pour former un bouclier défensif temporaire.', 2);

-- --------------------------------------------------------

--
-- Structure de la table `super_hero`
--

DROP TABLE IF EXISTS `super_hero`;
CREATE TABLE IF NOT EXISTS `super_hero` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alter_ego` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `est_disponible` tinyint(1) NOT NULL,
  `niveau_energie` int NOT NULL,
  `biographie` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_image_modif` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `cree_le` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `super_hero`
--

INSERT INTO `super_hero` (`id`, `nom`, `alter_ego`, `est_disponible`, `niveau_energie`, `biographie`, `nom_image`, `date_image_modif`, `cree_le`) VALUES
(67, 'Captain Vision', 'Victor Ray', 0, 90, 'Un ancien scientifique devenu un héros après un accident de laboratoire, il utilise sa Vision Laser pour protéger la ville.', 'hero1-6749816c474cd793581433.webp', '2024-11-29 08:55:08', '2024-11-29 08:54:00'),
(68, 'Phantom Shadow', 'Elise Noir', 0, 75, 'Une espionne qui a perfectionné l’art de l’invisibilité, elle agit dans l’ombre pour neutraliser les menaces.', 'hero3-674981b428e08853903698.webp', '2024-11-29 08:56:20', '2024-11-29 08:55:00'),
(69, 'Elementa', 'Sarah Vortex', 0, 85, 'Née avec un don unique, elle utilise son contrôle des éléments pour combattre les catastrophes naturelles et les ennemis.', 'hero4-674981e755944232879452.webp', '2024-11-29 08:57:11', '2024-11-29 08:56:00'),
(70, 'Warp Warrior', 'Ethan Blaze', 0, 80, 'Un aventurier intrépide capable de se téléporter à travers les dimensions pour résoudre des énigmes et protéger la Terre.', 'hero5-67498210a8480825825307.webp', '2024-11-29 08:57:52', '2024-11-29 08:57:00'),
(71, 'Pyrokinetic Master', 'Alex Sparks', 0, 70, 'Avec son contrôle du feu, il est le dernier espoir face à des invasions extraterrestres menaçant l’humanité.', 'hero6-67498234c4c1f898314408.webp', '2024-11-29 08:58:28', '2024-11-29 08:57:00'),
(72, 'Titaness Fury', 'Ava Titan', 0, 95, 'Titaness Fury est une guerrière géante née dans une tribu légendaire cachée dans les montagnes les plus inaccessibles de la planète. Dotée d\'une force incommensurable et d\'une endurance inégalée, elle est une protectrice farouche de la nature et des innocents. Sa sauvagerie en combat ne connaît pas de limite, mais son cœur est pur, et elle se bat uniquement pour la justice.', 'hero7-67498262e7239823741006.webp', '2024-11-29 08:59:14', '2024-11-29 08:58:00'),
(73, 'Kalor', 'Emjy Celemani', 0, 97, 'Kalor est un survivant de la destruction de Krypton. Élevé sous un soleil jaune, ses cellules absorbent l\'énergie solaire, lui conférant des pouvoirs extraordinaires. Il est animé par un profond désir de justice et de protection des innocents.', 'hero8-674985f467d15702552931.webp', '2024-11-29 09:14:28', '2024-11-29 09:09:00'),
(74, 'Azo', 'Theo AH-Yong', 0, 80, 'Un justicier de Gotham, sans super pouvoirs mais avec une volonté de fer.', 'hero9-674987577768d779186568.webp', '2024-11-29 09:20:23', '2024-11-29 09:19:00'),
(75, 'Nova Guardian', 'Lay Brown', 0, 91, 'Issue d’une planète mystérieuse, Nova Guardian est une guerrière inter dimensionnelle qui a choisi la Terre comme nouveau foyer. Elle protège l\'humanité avec un sens inébranlable de justice.', 'hero10-674989939c567377218291.webp', '2024-11-29 09:29:55', '2024-11-29 09:28:00');

-- --------------------------------------------------------

--
-- Structure de la table `super_hero_pouvoir`
--

DROP TABLE IF EXISTS `super_hero_pouvoir`;
CREATE TABLE IF NOT EXISTS `super_hero_pouvoir` (
  `super_hero_id` int NOT NULL,
  `pouvoir_id` int NOT NULL,
  PRIMARY KEY (`super_hero_id`,`pouvoir_id`),
  KEY `IDX_8E6512CBB62BE361` (`super_hero_id`),
  KEY `IDX_8E6512CBC8A705F8` (`pouvoir_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `super_hero_pouvoir`
--

INSERT INTO `super_hero_pouvoir` (`super_hero_id`, `pouvoir_id`) VALUES
(67, 1),
(67, 6),
(68, 5),
(68, 7),
(69, 8),
(70, 1),
(70, 5),
(71, 8),
(72, 1),
(72, 8),
(72, 9),
(73, 1),
(73, 10),
(73, 11),
(75, 1),
(75, 10),
(75, 12);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `FK_2449BA15150A48F1` FOREIGN KEY (`chef_id`) REFERENCES `super_hero` (`id`);

--
-- Contraintes pour la table `equipe_super_hero`
--
ALTER TABLE `equipe_super_hero`
  ADD CONSTRAINT `FK_970E3D1E6D861B89` FOREIGN KEY (`equipe_id`) REFERENCES `equipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_970E3D1EB62BE361` FOREIGN KEY (`super_hero_id`) REFERENCES `super_hero` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mission`
--
ALTER TABLE `mission`
  ADD CONSTRAINT `FK_9067F23C937C8CB0` FOREIGN KEY (`equipe_assignee_id`) REFERENCES `equipe` (`id`);

--
-- Contraintes pour la table `mission_pouvoir`
--
ALTER TABLE `mission_pouvoir`
  ADD CONSTRAINT `FK_2F43FA8BBE6CAE90` FOREIGN KEY (`mission_id`) REFERENCES `mission` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_2F43FA8BC8A705F8` FOREIGN KEY (`pouvoir_id`) REFERENCES `pouvoir` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `super_hero_pouvoir`
--
ALTER TABLE `super_hero_pouvoir`
  ADD CONSTRAINT `FK_8E6512CBB62BE361` FOREIGN KEY (`super_hero_id`) REFERENCES `super_hero` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_8E6512CBC8A705F8` FOREIGN KEY (`pouvoir_id`) REFERENCES `pouvoir` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
