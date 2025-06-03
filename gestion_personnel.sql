-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 03, 2025 at 09:57 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_personnel`
--

-- --------------------------------------------------------

--
-- Table structure for table `absence`
--

DROP TABLE IF EXISTS `absence`;
CREATE TABLE IF NOT EXISTS `absence` (
  `N__ABS` int NOT NULL AUTO_INCREMENT,
  `ID_EMP` int NOT NULL,
  `DUREE_ABS` int DEFAULT NULL,
  `DATE` date DEFAULT NULL,
  `MOTIF` text,
  `JUSTIF` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`N__ABS`),
  KEY `FK_ABSENCE_ASSOCIATI_EMPLOYEE` (`ID_EMP`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `absence`
--

INSERT INTO `absence` (`N__ABS`, `ID_EMP`, `DUREE_ABS`, `DATE`, `MOTIF`, `JUSTIF`) VALUES
(10, 14, 5, '2030-10-10', 'mrid', 'non justifier');

-- --------------------------------------------------------

--
-- Table structure for table `attestation`
--

DROP TABLE IF EXISTS `attestation`;
CREATE TABLE IF NOT EXISTS `attestation` (
  `ID_ATTESTATION` int NOT NULL AUTO_INCREMENT,
  `ID_EMP` int NOT NULL,
  `MOTIF` text NOT NULL,
  `DATE_DEMANDE` date NOT NULL,
  `STATUT` varchar(20) DEFAULT 'En attente',
  `DATE_ATTESTATION` date NOT NULL,
  PRIMARY KEY (`ID_ATTESTATION`),
  KEY `FK_ATTESTATION_EMPLOYEE` (`ID_EMP`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attestation`
--

INSERT INTO `attestation` (`ID_ATTESTATION`, `ID_EMP`, `MOTIF`, `DATE_DEMANDE`, `STATUT`, `DATE_ATTESTATION`) VALUES
(1, 14, 'hh', '2025-05-18', 'Acceptée', '2025-05-18'),
(2, 14, 'hh', '2025-05-18', 'Acceptée', '2025-05-18'),
(4, 14, 'hh', '2025-06-01', 'En attente', '0000-00-00'),
(5, 14, 'SQS', '2025-06-01', 'En attente', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `conge`
--

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `N__CGE` int NOT NULL AUTO_INCREMENT,
  `ID_EMP` int NOT NULL,
  `DATE_DEBUT_CGE` date DEFAULT NULL,
  `DATE_FIN_CGE` date DEFAULT NULL,
  `ETAT` varchar(255) NOT NULL,
  PRIMARY KEY (`N__CGE`),
  KEY `FK_CONGE_PRENDRE_EMPLOYEE` (`ID_EMP`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `conge`
--

INSERT INTO `conge` (`N__CGE`, `ID_EMP`, `DATE_DEBUT_CGE`, `DATE_FIN_CGE`, `ETAT`) VALUES
(24, 18, '2025-06-01', '2025-06-05', 'accepted'),
(23, 18, '2025-05-18', '2025-05-28', 'accepted'),
(22, 17, '2025-06-01', '2025-06-08', 'accepted'),
(21, 17, '2025-05-18', '2025-05-28', 'accepted'),
(20, 14, '2025-05-19', '2025-05-21', 'accepted'),
(25, 18, '2025-07-10', '2025-07-18', 'accepted'),
(26, 20, '2025-05-18', '2025-05-20', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `contrat`
--

DROP TABLE IF EXISTS `contrat`;
CREATE TABLE IF NOT EXISTS `contrat` (
  `N__CONTRAT` int NOT NULL AUTO_INCREMENT,
  `ID_EMP` int NOT NULL,
  `TYPE_CONTRAT` varchar(40) DEFAULT NULL,
  `FONCTION` varchar(20) DEFAULT NULL,
  `QUALIFICATION` varchar(30) DEFAULT NULL,
  `CATEGORIE` varchar(40) DEFAULT NULL,
  `ECHLAN` varchar(10) DEFAULT NULL,
  `NBR_H_MOIS` int DEFAULT NULL,
  `NBR_H___JOURS` int DEFAULT NULL,
  `TYPE_DE_PAIE` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`N__CONTRAT`),
  KEY `FK_CONTRAT_DETENIR_EMPLOYEE` (`ID_EMP`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contrat`
--

INSERT INTO `contrat` (`N__CONTRAT`, `ID_EMP`, `TYPE_CONTRAT`, `FONCTION`, `QUALIFICATION`, `CATEGORIE`, `ECHLAN`, `NBR_H_MOIS`, `NBR_H___JOURS`, `TYPE_DE_PAIE`) VALUES
(1, 14, 'CDI', 'IDK', 'idk', 'idk', 'idk', 0, 0, 'idk');

-- --------------------------------------------------------

--
-- Table structure for table `diplome`
--

DROP TABLE IF EXISTS `diplome`;
CREATE TABLE IF NOT EXISTS `diplome` (
  `ID_DPL` int NOT NULL AUTO_INCREMENT,
  `ID_TYPE_DPL` int NOT NULL,
  `SPECIALITE_DPL` varchar(30) DEFAULT NULL,
  `MENTION_DPL` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID_DPL`),
  KEY `FK_DIPLOME_ASSOCIATI_TYPE_DIP` (`ID_TYPE_DPL`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `diplome`
--

INSERT INTO `diplome` (`ID_DPL`, `ID_TYPE_DPL`, `SPECIALITE_DPL`, `MENTION_DPL`) VALUES
(1, 1, 'devlopment', '16'),
(2, 1, 'devlopment', '16'),
(3, 1, 'DB', '17');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `ID_EMP` int NOT NULL AUTO_INCREMENT,
  `N__SERVICE` int NOT NULL,
  `NOM_EMP` varchar(20) DEFAULT NULL,
  `PRENOM_EMP` varchar(20) DEFAULT NULL,
  `DATE_EMP` date DEFAULT NULL,
  `TEL_EMP` varchar(20) DEFAULT NULL,
  `EMAIL_EMP` varchar(30) DEFAULT NULL,
  `ADRESSE_EMP` varchar(50) DEFAULT NULL,
  `DATEEMBAUCH_EMP` date DEFAULT NULL,
  `NOMBRE_D_ENFANT` int DEFAULT NULL,
  `NOM_UTILISATEUR` varchar(20) DEFAULT NULL,
  `MOT_DE_PASSE` varchar(40) DEFAULT NULL,
  `ROLES` varchar(20) DEFAULT NULL,
  `NOMBRE_JOURS_CONGE` int NOT NULL DEFAULT '30',
  PRIMARY KEY (`ID_EMP`),
  UNIQUE KEY `NOM_UTILISATEUR_2` (`NOM_UTILISATEUR`),
  KEY `FK_EMPLOYEE_TRAVAILLE_SERVICE` (`N__SERVICE`),
  KEY `NOM_UTILISATEUR` (`NOM_UTILISATEUR`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`ID_EMP`, `N__SERVICE`, `NOM_EMP`, `PRENOM_EMP`, `DATE_EMP`, `TEL_EMP`, `EMAIL_EMP`, `ADRESSE_EMP`, `DATEEMBAUCH_EMP`, `NOMBRE_D_ENFANT`, `NOM_UTILISATEUR`, `MOT_DE_PASSE`, `ROLES`, `NOMBRE_JOURS_CONGE`) VALUES
(14, 2, 'benzattat', 'oussama', '2025-04-23', '43636436', 'gzgzg@gmail.com', 'AZZUOZIA', '2025-04-23', 19, 'oussama12', '0000', 'employee', 24),
(13, 1, 'aal', 'abdellah', '2003-05-14', '06123124', 'abdou.one.black@gmail.com', 'AFAQ', '2025-04-26', 1, 'abdellah', 'aalaou', 'admin', 30),
(18, 2, 'simo', 'works', '2025-05-18', '532525', 'gzgzg@gmail.com', 'AFAQ', '2025-05-08', 3, 'ayman', '666', 'employee', 21),
(20, 2, 'ayman', 'sahafi', '2025-05-07', '532525', 'gzgzg@gmail.com', 'AFAQ', '2025-05-28', 3, 'ben10', '0000', 'employee', 27),
(34, 2, 'sa', 'ayman', '2025-06-19', '532525', 'gzgzg@gmail.com', 'aaagazy.com', '2025-06-19', 0, 'simo6', '123', 'employee', 30),
(32, 1, 'tjriba', 'siir', '2025-06-27', '532525', 'zgzg@trh', 'aaagazy.com', '2025-06-27', 0, 'siro', '123', 'employee', 30);

-- --------------------------------------------------------

--
-- Table structure for table `employee_diplome`
--

DROP TABLE IF EXISTS `employee_diplome`;
CREATE TABLE IF NOT EXISTS `employee_diplome` (
  `ID_DPL` int NOT NULL,
  `ID_EMP` int NOT NULL,
  `DATE_OBTENTION_DIPLOME` date DEFAULT NULL,
  PRIMARY KEY (`ID_DPL`,`ID_EMP`),
  KEY `FK_EMP_DIP_EMPLOYEE` (`ID_EMP`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee_diplome`
--

INSERT INTO `employee_diplome` (`ID_DPL`, `ID_EMP`, `DATE_OBTENTION_DIPLOME`) VALUES
(1, 13, '2025-04-03'),
(2, 13, '2025-04-03'),
(3, 14, '2025-06-06');

-- --------------------------------------------------------

--
-- Table structure for table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `ID_ENTREPRISE` int NOT NULL AUTO_INCREMENT,
  `DENOMINATION` varchar(50) DEFAULT NULL,
  `FORMEJURIDIQUE` varchar(50) DEFAULT NULL,
  `IDENTIFIANT_FISCAL` varchar(30) DEFAULT NULL,
  `N_DE_REGISTRE` varchar(30) DEFAULT NULL,
  `ACTIVITE` varchar(60) DEFAULT NULL,
  `ADRESSE` varchar(50) DEFAULT NULL,
  `TEL` varchar(20) DEFAULT NULL,
  `EMAIL` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID_ENTREPRISE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salaire`
--

DROP TABLE IF EXISTS `salaire`;
CREATE TABLE IF NOT EXISTS `salaire` (
  `ID_SLR` int NOT NULL AUTO_INCREMENT,
  `ID_EMP` int NOT NULL,
  `DATE_PAIEMENT` date DEFAULT NULL,
  `MONTANT` int DEFAULT NULL,
  PRIMARY KEY (`ID_SLR`),
  KEY `FK_SALAIRE_ASSOCIATI_EMPLOYEE` (`ID_EMP`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `salaire`
--

INSERT INTO `salaire` (`ID_SLR`, `ID_EMP`, `DATE_PAIEMENT`, `MONTANT`) VALUES
(5, 14, '2025-07-11', 3000);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `N__SERVICE` int NOT NULL AUTO_INCREMENT,
  `ID_ENTREPRISE` int NOT NULL,
  `NOM_SERVICE` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`N__SERVICE`),
  KEY `FK_SERVICE_APPARTENI_ENTREPRI` (`ID_ENTREPRISE`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`N__SERVICE`, `ID_ENTREPRISE`, `NOM_SERVICE`) VALUES
(1, 200, 'clientele'),
(2, 200, 'INFORMATIQUE'),
(3, 200, 'BUREAUTIQUE');

-- --------------------------------------------------------

--
-- Table structure for table `type_diplome`
--

DROP TABLE IF EXISTS `type_diplome`;
CREATE TABLE IF NOT EXISTS `type_diplome` (
  `ID_TYPE_DPL` int NOT NULL AUTO_INCREMENT,
  `NOM_TYPE_DPL` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID_TYPE_DPL`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `type_diplome`
--

INSERT INTO `type_diplome` (`ID_TYPE_DPL`, `NOM_TYPE_DPL`) VALUES
(1, 'ssssss');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
