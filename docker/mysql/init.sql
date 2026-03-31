-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 27 mars 2026 à 14:48
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
  `ML` varchar(255) DEFAULT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Apply`
--

INSERT INTO `Apply` (`ID_user`, `ID_offer`, `CV`, `ML`, `comment`) VALUES
(1, 1, 'cv1.pdf', 'lettre1', 'motivé'),
(1, 7, 'cv1.pdf', 'lettre4', 'passion IA'),
(2, 2, 'cv2.pdf', 'lettre2', 'serieux'),
(3, 6, 'cv3.pdf', 'lettre3', 'data lover');

-- --------------------------------------------------------

--
-- Structure de la table `Company`
--

CREATE TABLE `Company` (
  `ID_company` int NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Create_date` date DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Phone_Number` varchar(10) DEFAULT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Company`
--

INSERT INTO `Company` (`ID_company`, `Name`, `Create_date`, `Email`, `Phone_Number`, `Description`) VALUES
(1, 'TechCorp', '2020-01-01', 'contact@techcorp.com', '0102030405', 'Entreprise web'),
(2, 'DataSolutions', '2019-05-10', 'hr@data.com', '0203040506', 'Data & IA'),
(3, 'GreenEnergy', '2018-03-15', 'contact@green.com', '0304050607', 'Energie renouvelable');

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

--
-- Déchargement des données de la table `Evaluate`
--

INSERT INTO `Evaluate` (`ID_user`, `ID_company`, `Rate`, `Comment`) VALUES
(1, 1, 5, 'Top'),
(1, 3, 5, 'Excellent'),
(2, 1, 4, 'Bien'),
(3, 2, 3, 'Correct');

-- --------------------------------------------------------

--
-- Structure de la table `Job_offer`
--

CREATE TABLE `Job_offer` (
  `ID_offer` int NOT NULL,
  `Title` text,
  `Description` text,
  `Salary` decimal(10,2) DEFAULT NULL,
  `Duration` int DEFAULT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Create_date` date DEFAULT NULL,
  `Start_date` date DEFAULT NULL,
  `ID_company` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Job_offer`
--

INSERT INTO `Job_offer` (`ID_offer`, `Title`, `Description`, `Salary`, `Duration`, `Category`, `Create_date`, `Start_date`, `ID_company`) VALUES
(1, 'Développeur Web', 'PHP / MySQL', 2000.00, 6, 'Informatique', '2026-03-01', '2026-04-01', 1),
(2, 'Front-End', 'React JS', 1800.00, 6, 'Informatique', '2026-03-01', '2026-04-01', 1),
(3, 'Back-End', 'Laravel API', 2100.00, 6, 'Informatique', '2026-02-01', '2026-03-01', 1),
(4, 'Full Stack', 'Web complet', 2200.00, 6, 'Informatique', '2026-01-01', '2026-02-01', 1),
(5, 'Dev Mobile', 'Flutter', 2000.00, 6, 'Informatique', '2026-02-01', '2026-03-01', 1),
(6, 'Data Analyst', 'Python SQL', 2200.00, 6, 'Data', '2026-02-01', '2026-03-01', 2),
(7, 'Data Scientist', 'Machine Learning', 2400.00, 6, 'Data', '2026-01-01', '2026-02-01', 2),
(8, 'BI Analyst', 'Power BI', 2100.00, 5, 'Data', '2026-03-01', '2026-04-01', 2),
(9, 'Data Engineer', 'Pipeline data', 2300.00, 6, 'Data', '2026-02-01', '2026-03-01', 2),
(10, 'AI Engineer', 'Deep Learning', 2500.00, 6, 'Data', '2026-01-01', '2026-02-01', 2),
(11, 'Ingénieur Energie', 'Optimisation', 2300.00, 6, 'Energie', '2026-01-01', '2026-02-01', 3),
(12, 'Technicien Energie', 'Maintenance', 1800.00, 6, 'Energie', '2026-02-01', '2026-03-01', 3),
(13, 'Projet Environnement', 'Gestion projet', 2000.00, 6, 'Energie', '2026-03-01', '2026-04-01', 3),
(14, 'Consultant RSE', 'Développement durable', 2100.00, 6, 'Energie', '2026-01-01', '2026-02-01', 3),
(15, 'Thermicien', 'Bâtiment', 2400.00, 6, 'Energie', '2026-02-01', '2026-03-01', 3),
(16, 'DevOps', 'Docker CI/CD', 2400.00, 6, 'Informatique', '2026-03-01', '2026-04-01', 1),
(17, 'Cybersecurity', 'Sécurité SI', 2500.00, 6, 'Informatique', '2026-02-01', '2026-03-01', 2),
(18, 'QA Tester', 'Tests logiciels', 1800.00, 4, 'Informatique', '2026-01-01', '2026-02-01', 1),
(19, 'Product Owner', 'Agile', 2200.00, 6, 'Management', '2026-03-01', '2026-04-01', 2),
(20, 'Scrum Master', 'Gestion agile', 2300.00, 6, 'Management', '2026-02-01', '2026-03-01', 1);

-- --------------------------------------------------------

--
-- Structure de la table `Profil`
--

CREATE TABLE `Profil` (
  `ID_profil` int NOT NULL,
  `Name` varchar(50) DEFAULT NULL,
  `Lastname` varchar(50) DEFAULT NULL,
  `Phone_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Profil`
--

INSERT INTO `Profil` (`ID_profil`, `Name`, `Lastname`, `Phone_number`) VALUES
(1, 'Mathis', 'Boulenger', '0600000001'),
(2, 'Lou', 'Dutertre', '0600000002'),
(3, 'Nathan', 'Bocquet', '0600000003'),
(4, 'Ines', 'Lancelevee', '0600000004'),
(5, 'Seohyun', 'Nam', '0600000005'),
(6, 'Admin', 'Root', '0600000006');

-- --------------------------------------------------------

--
-- Structure de la table `Role`
--

CREATE TABLE `Role` (
  `ID_role` int NOT NULL,
  `Status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Role`
--

INSERT INTO `Role` (`ID_role`, `Status`) VALUES
(1, 'etudiant'),
(2, 'recruteur'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `User_`
--

CREATE TABLE `User_` (
  `ID_user` int NOT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Password` varchar(60) DEFAULT NULL,
  `ID_profil` int DEFAULT NULL,
  `ID_role` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `User_`
--

INSERT INTO `User_` (`ID_user`, `Email`, `Password`, `ID_profil`, `ID_role`) VALUES
(1, 'mathis@mail.com', '1234', 1, 1),
(2, 'lou@mail.com', '1234', 2, 1),
(3, 'nathan@mail.com', '1234', 3, 1),
(4, 'ines@mail.com', '1234', 4, 2),
(5, 'seo@mail.com', '1234', 5, 2),
(6, 'admin@mail.com', 'admin', 6, 3);

-- --------------------------------------------------------

--
-- Structure de la table `Wishlist`
--

CREATE TABLE `Wishlist` (
  `ID_user` int NOT NULL,
  `ID_offer` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Wishlist`
--

INSERT INTO `Wishlist` (`ID_user`, `ID_offer`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(1, 5);

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
  ADD PRIMARY KEY (`ID_company`);

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
  ADD PRIMARY KEY (`ID_role`);

--
-- Index pour la table `User_`
--
ALTER TABLE `User_`
  ADD PRIMARY KEY (`ID_user`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ID_profil` (`ID_profil`),
  ADD KEY `ID_role` (`ID_role`);

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
  MODIFY `ID_company` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  MODIFY `ID_offer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `Profil`
--
ALTER TABLE `Profil`
  MODIFY `ID_profil` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `Role`
--
ALTER TABLE `Role`
  MODIFY `ID_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `User_`
--
ALTER TABLE `User_`
  MODIFY `ID_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Contraintes pour la table `Evaluate`
--
ALTER TABLE `Evaluate`
  ADD CONSTRAINT `Evaluate_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `User_` (`ID_user`),
  ADD CONSTRAINT `Evaluate_ibfk_2` FOREIGN KEY (`ID_company`) REFERENCES `Company` (`ID_company`);

--
-- Contraintes pour la table `Job_offer`
--
ALTER TABLE `Job_offer`
  ADD CONSTRAINT `Job_offer_ibfk_1` FOREIGN KEY (`ID_company`) REFERENCES `Company` (`ID_company`);

--
-- Contraintes pour la table `User_`
--
ALTER TABLE `User_`
  ADD CONSTRAINT `User__ibfk_1` FOREIGN KEY (`ID_profil`) REFERENCES `Profil` (`ID_profil`),
  ADD CONSTRAINT `User__ibfk_2` FOREIGN KEY (`ID_role`) REFERENCES `Role` (`ID_role`);

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
