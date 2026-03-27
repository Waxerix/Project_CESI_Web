-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : sam. 21 mars 2026 à 14:25
-- Version du serveur : 8.0.45-0ubuntu0.24.04.1
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_web`
--

-- --------------------------------------------------------

--
-- Structure de la table `Apply`
--

CREATE TABLE `Apply` (
  `ID_user` int NOT NULL,
  `ID_offer` int NOT NULL,
  `CV` varchar(255) DEFAULT NULL,
  `ML` text,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Company`
--

CREATE TABLE `Company` (
  `ID_company` int NOT NULL,
  `Name` varchar(150) DEFAULT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Phone_Number` varchar(20) DEFAULT NULL,
  `Description` text,
  `Date_` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Date_`
--

CREATE TABLE `Date_` (
  `Date_` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Evaluate`
--

CREATE TABLE `Evaluate` (
  `ID_user` int NOT NULL,
  `ID_company` int NOT NULL,
  `Rate` int DEFAULT NULL,
  `Comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Job_offer`
--

CREATE TABLE `Job_offer` (
  `ID_offer` int NOT NULL,
  `Title` varchar(150) DEFAULT NULL,
  `Description` text,
  `Salary` decimal(10,2) DEFAULT NULL,
  `Duration` varchar(50) DEFAULT NULL,
  `Date_` date DEFAULT NULL,
  `Date__1` date DEFAULT NULL,
  `Date__2` date DEFAULT NULL,
  `ID_company` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Profil`
--

CREATE TABLE `Profil` (
  `ID_profil` int NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Lastname` varchar(100) DEFAULT NULL,
  `Phone_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Profil`
--

INSERT INTO `Profil` (`ID_profil`, `Name`, `Lastname`, `Phone_number`) VALUES
(9, 'mathis', 'boulenger', '0607258033'),
(10, 'Lou', 'DUTERTRE-THOUAN', '0607258033'),
(11, 'Seohyun', 'Nam', '65464546'),
(12, 'Shahineze', 'Kadiri', '0766023865'),
(13, 'Galaad', 'Goutier', '0667676767'),
(14, 'Inès ', 'Lancelevée ', '0769566600'),
(15, 'Nathan', 'Bocquet', '0651452909');

-- --------------------------------------------------------

--
-- Structure de la table `Role`
--

CREATE TABLE `Role` (
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Role`
--

INSERT INTO `Role` (`Status`) VALUES
('étudiant'),
('pilote'),
('tuteur');

-- --------------------------------------------------------

--
-- Structure de la table `User_`
--

CREATE TABLE `User_` (
  `ID_user` int NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `ID_profil` int DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `User_`
--

INSERT INTO `User_` (`ID_user`, `Email`, `Password`, `ID_profil`, `status`) VALUES
(7, 'mathisboulenger@gmail.com', '$2y$10$GCrPA3sUPTXKdJMZA1e0.u582IJYOlt2RjUI9ME67Aqmd0.XvoPNO', 9, 'étudiant'),
(8, 'tytylo281@gmail.com', '$2y$10$JAoV0sX/8nKUoSrqxyOtAO4xhy0OhtacfyHKb7mJ2srxM0AjX/wIG', 10, 'étudiant'),
(9, 'sdfsf@gmail.com', '$2y$10$tXHKg8Q/m1m08rWB8KK6/.jLuX4BXnfmLHkhEP8lnh.eYKiohamLe', 11, 'étudiant'),
(10, 'shahineze.kadiri@viacesi.fr', '$2y$10$GaDympBANJDf6FwEcSijs.wpw.sa/EB2Oi/DtrF2KBU9jmkzQNM26', 12, 'étudiant'),
(11, 'galaad.goutier@gmail.com', '$2y$10$eYxcemE/Yva8S0LFpuPrCeww/N.HXTxhMokm2YB3OqPzDd7xFYQIe', 13, 'étudiant'),
(12, 'ines.lance76@gmail.com', '$2y$10$wzZXdAHLYvjOR/uB5IpgqebdqHMBaUrB80ZntOdWA41swf4IKrndK', 14, 'pilote'),
(13, 'nathan.bocquetp@gmail.com', '$2y$10$/1//aA8woz58k4AaBXZVF.foeXL29i95yYhU.uD/Fzin90j4pjwGO', 15, 'étudiant');

-- --------------------------------------------------------

--
-- Structure de la table `Wishlist`
--

CREATE TABLE `Wishlist` (
  `ID_user` int NOT NULL,
  `ID_offer` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Apply`
--
ALTER TABLE `Apply`
  ADD PRIMARY KEY (`ID_user`,`ID_offer`),
  ADD KEY `ID_offer` (`ID_offer`);

--
-- Index pour la table `Company`
--
ALTER TABLE `Company`
  ADD PRIMARY KEY (`ID_company`),
  ADD KEY `Date_` (`Date_`);

--
-- Index pour la table `Date_`
--
ALTER TABLE `Date_`
  ADD PRIMARY KEY (`Date_`);

--
-- Index pour la table `Evaluate`
--
ALTER TABLE `Evaluate`
  ADD PRIMARY KEY (`ID_user`,`ID_company`),
  ADD KEY `ID_company` (`ID_company`);

--
-- Index pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  ADD PRIMARY KEY (`ID_offer`),
  ADD KEY `Date_` (`Date_`),
  ADD KEY `Date__1` (`Date__1`),
  ADD KEY `Date__2` (`Date__2`),
  ADD KEY `ID_company` (`ID_company`);

--
-- Index pour la table `Profil`
--
ALTER TABLE `Profil`
  ADD PRIMARY KEY (`ID_profil`);

--
-- Index pour la table `Role`
--
ALTER TABLE `Role`
  ADD PRIMARY KEY (`Status`);

--
-- Index pour la table `User_`
--
ALTER TABLE `User_`
  ADD PRIMARY KEY (`ID_user`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ID_profil` (`ID_profil`),
  ADD KEY `status` (`status`);

--
-- Index pour la table `Wishlist`
--
ALTER TABLE `Wishlist`
  ADD PRIMARY KEY (`ID_user`,`ID_offer`),
  ADD KEY `ID_offer` (`ID_offer`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Company`
--
ALTER TABLE `Company`
  MODIFY `ID_company` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  MODIFY `ID_offer` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Profil`
--
ALTER TABLE `Profil`
  MODIFY `ID_profil` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `User_`
--
ALTER TABLE `User_`
  MODIFY `ID_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Apply`
--
ALTER TABLE `Apply`
  ADD CONSTRAINT `Apply_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Apply_ibfk_2` FOREIGN KEY (`ID_offer`) REFERENCES `Job_offer` (`ID_offer`);

--
-- Contraintes pour la table `Company`
--
ALTER TABLE `Company`
  ADD CONSTRAINT `Company_ibfk_1` FOREIGN KEY (`Date_`) REFERENCES `Date_` (`Date_`);

--
-- Contraintes pour la table `Evaluate`
--
ALTER TABLE `Evaluate`
  ADD CONSTRAINT `Evaluate_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Evaluate_ibfk_2` FOREIGN KEY (`ID_company`) REFERENCES `Company` (`ID_company`);

--
-- Contraintes pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  ADD CONSTRAINT `Job_offer_ibfk_1` FOREIGN KEY (`Date_`) REFERENCES `Date_` (`Date_`),
  ADD CONSTRAINT `Job_offer_ibfk_2` FOREIGN KEY (`Date__1`) REFERENCES `Date_` (`Date_`),
  ADD CONSTRAINT `Job_offer_ibfk_3` FOREIGN KEY (`Date__2`) REFERENCES `Date_` (`Date_`),
  ADD CONSTRAINT `Job_offer_ibfk_4` FOREIGN KEY (`ID_company`) REFERENCES `Company` (`ID_company`);

--
-- Contraintes pour la table `User_`
--
ALTER TABLE `User_`
  ADD CONSTRAINT `User__ibfk_1` FOREIGN KEY (`ID_profil`) REFERENCES `Profil` (`ID_profil`),
  ADD CONSTRAINT `User__ibfk_2` FOREIGN KEY (`status`) REFERENCES `Role` (`Status`);

--
-- Contraintes pour la table `Wishlist`
--
ALTER TABLE `Wishlist`
  ADD CONSTRAINT `Wishlist_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Wishlist_ibfk_2` FOREIGN KEY (`ID_offer`) REFERENCES `Job_offer` (`ID_offer`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
