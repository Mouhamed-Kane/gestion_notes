-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 24 avr. 2025 à 19:49
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
-- Base de données : `gestion_notes_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `matricule` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `formation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `matricule`, `mot_de_passe`, `nom`, `prenom`, `adresse`, `telephone`, `formation_id`) VALUES
(4, 'ETU001', '$2y$10$hV.VAUFx9MGFYQqlJCB8K.ejn5NubtlMN7aFLK7gf6SYpOmBBEw4W', 'Test', 'Etudiant', '1 rue du test', '0600000000', 1),
(5, 'ETU002', '$2y$10$bFw6Ne3h27bjJ1mMUgY0GO1eHLF9foYKBzrm53pxFSUefn.hhfRn2', 'Seck', 'Laye', 'Thies', '77123456', 2);

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formations`
--

INSERT INTO `formations` (`id`, `code`, `libelle`) VALUES
(1, '#02', 'Informatique'),
(2, '#01', 'Management des entreprises');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `coefficient` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `code`, `libelle`, `formation_id`, `coefficient`) VALUES
(1, '#0001', 'Anglais', NULL, 4);

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) DEFAULT NULL,
  `matiere_id` int(11) DEFAULT NULL,
  `note` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `etudiant_id`, `matiere_id`, `note`) VALUES
(1, 4, 1, 18.00),
(2, 5, 1, 18.00);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('administrateur','etudiant') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `email`, `mot_de_passe`, `role`) VALUES
(3, 'admin@demo.com', '$2y$10$hV.VAUFx9MGFYQqlJCB8K.ejn5NubtlMN7aFLK7gf6SYpOmBBEw4W', 'administrateur'),
(4, 'ETU001', '$2y$10$P2FjQX6p8Z7Vw5K0Xz2wzOQwNQb2h6r9v4JX2Z9zQwTz7K0Xz2wzO', 'etudiant'),
(5, 'mkane1510@gmail.com', '123456789', 'administrateur'),
(6, 'hello@hello.com', '$2y$10$9E5g.V3B0U1GG18dQ0m4te8UsS95VvIaZp9xmpO0EE2k3NboA/T1S', 'administrateur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`),
  ADD KEY `matiere_id` (`matiere_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD CONSTRAINT `matieres_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
