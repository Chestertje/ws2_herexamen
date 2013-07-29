-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 19 jul 2013 om 13:15
-- Serverversie: 5.5.24-log
-- PHP-versie: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `immo`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bebouwing_types`
--

CREATE TABLE IF NOT EXISTS `bebouwing_types` (
  `Bebouwingtype_id` int(11) NOT NULL AUTO_INCREMENT,
  `Type` varchar(45) NOT NULL,
  PRIMARY KEY (`Bebouwingtype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `bebouwing_types`
--

INSERT INTO `bebouwing_types` (`Bebouwingtype_id`, `Type`) VALUES
(1, 'Open'),
(2, 'Half open'),
(3, 'Gesloten');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `makelaars`
--

CREATE TABLE IF NOT EXISTS `makelaars` (
  `Makelaar_id` int(11) NOT NULL AUTO_INCREMENT,
  `Provincie_id` int(11) DEFAULT NULL,
  `Bedrijf_naam` varchar(255) NOT NULL,
  `Contact_email` varchar(255) NOT NULL,
  `Voornaam` varchar(255) NOT NULL,
  `Achternaam` varchar(255) NOT NULL,
  `Paswoord` varchar(255) NOT NULL,
  `Locatie` varchar(255) DEFAULT NULL,
  `Logo` varchar(100) DEFAULT NULL,
  `Beschrijving` text,
  PRIMARY KEY (`Makelaar_id`),
  KEY `fk_Makelaars_Provincies` (`Provincie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `provincies`
--

CREATE TABLE IF NOT EXISTS `provincies` (
  `Provincie_id` int(11) NOT NULL AUTO_INCREMENT,
  `Provincie` varchar(100) NOT NULL,
  PRIMARY KEY (`Provincie_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Gegevens worden uitgevoerd voor tabel `provincies`
--

INSERT INTO `provincies` (`Provincie_id`, `Provincie`) VALUES
(1, 'Antwerpen'),
(2, 'Limburg'),
(3, 'Oost-Vlaanderen'),
(4, 'Vlaams Brabant'),
(5, 'West-Vlaanderen'),
(6, 'Hainaut'),
(7, 'Liége'),
(8, 'Brabant Wallon'),
(9, 'Luxembourg'),
(10, 'Namur'),
(23, 'Brussel');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `Status_id` int(11) NOT NULL AUTO_INCREMENT,
  `Status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Gegevens worden uitgevoerd voor tabel `status`
--

INSERT INTO `status` (`Status_id`, `Status`) VALUES
(1, 'Te koop'),
(2, 'Te huur'),
(3, 'Verkocht'),
(4, 'Verhuurd');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vastgoed`
--

CREATE TABLE IF NOT EXISTS `vastgoed` (
  `Vastgoed_id` int(11) NOT NULL AUTO_INCREMENT,
  `Makelaar_id` int(11) NOT NULL,
  `Prijs` int(11) NOT NULL,
  `Locatie` varchar(255) NOT NULL,
  `Provincie_id` int(11) NOT NULL,
  `Straat` varchar(255) NOT NULL,
  `Vastgoedtype_id` int(11) NOT NULL,
  `Bebouwingtype_id` int(11) DEFAULT NULL,
  `Status_id` int(11) NOT NULL,
  `Aantal_slaapkamers` int(11) DEFAULT NULL,
  `Aantal_kamers` int(11) DEFAULT NULL,
  `Oppervlakte` int(11) DEFAULT NULL,
  `Garage` tinyint(1) DEFAULT NULL,
  `Bouwjaar` year(4) DEFAULT NULL,
  `Beschrijving` text,
  PRIMARY KEY (`Vastgoed_id`,`Makelaar_id`),
  KEY `fk_Vastgoed_Makelaars1` (`Makelaar_id`),
  KEY `fk_Vastgoed_Provincies1` (`Provincie_id`),
  KEY `fk_Vastgoed_Woningtypes1` (`Vastgoedtype_id`),
  KEY `fk_Vastgoed_Bebouwing_types1` (`Bebouwingtype_id`),
  KEY `fk_Vastgoed_Status1` (`Status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `vastgoedtypes`
--

CREATE TABLE IF NOT EXISTS `vastgoedtypes` (
  `Vastgoedtype_id` int(11) NOT NULL AUTO_INCREMENT,
  `Type` varchar(60) NOT NULL,
  PRIMARY KEY (`Vastgoedtype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Gegevens worden uitgevoerd voor tabel `vastgoedtypes`
--

INSERT INTO `vastgoedtypes` (`Vastgoedtype_id`, `Type`) VALUES
(1, 'Woning'),
(2, 'Appartement'),
(3, 'Grond'),
(4, 'Kot/Kamer'),
(5, 'Bedrijfsvastgoed'),
(6, 'Garage');

--
-- Beperkingen voor gedumpte tabellen
--

--
-- Beperkingen voor tabel `makelaars`
--
ALTER TABLE `makelaars`
  ADD CONSTRAINT `fk_Makelaars_Provincies` FOREIGN KEY (`Provincie_id`) REFERENCES `provincies` (`Provincie_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Beperkingen voor tabel `vastgoed`
--
ALTER TABLE `vastgoed`
  ADD CONSTRAINT `fk_Vastgoed_Makelaars1` FOREIGN KEY (`Makelaar_id`) REFERENCES `makelaars` (`Makelaar_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Provincies1` FOREIGN KEY (`Provincie_id`) REFERENCES `provincies` (`Provincie_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Woningtypes1` FOREIGN KEY (`Vastgoedtype_id`) REFERENCES `vastgoedtypes` (`Vastgoedtype_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Bebouwing_types1` FOREIGN KEY (`Bebouwingtype_id`) REFERENCES `bebouwing_types` (`Bebouwingtype_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Status1` FOREIGN KEY (`Status_id`) REFERENCES `status` (`Status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
