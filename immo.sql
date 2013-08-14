-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 14 aug 2013 om 08:54
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
  `Telefoon_nr` varchar(20) DEFAULT NULL,
  `Voornaam` varchar(255) NOT NULL,
  `Achternaam` varchar(255) NOT NULL,
  `Paswoord` varchar(255) NOT NULL,
  `Locatie` varchar(255) DEFAULT NULL,
  `Straat` varchar(255) DEFAULT NULL,
  `Logo` varchar(100) DEFAULT 'no.jpg',
  `Beschrijving` text,
  PRIMARY KEY (`Makelaar_id`),
  KEY `fk_Makelaars_Provincies` (`Provincie_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Gegevens worden uitgevoerd voor tabel `makelaars`
--

INSERT INTO `makelaars` (`Makelaar_id`, `Provincie_id`, `Bedrijf_naam`, `Contact_email`, `Telefoon_nr`, `Voornaam`, `Achternaam`, `Paswoord`, `Locatie`, `Straat`, `Logo`, `Beschrijving`) VALUES
(1, 1, 'Chester''s Company', 'chester@chester.com', '09/348.06.08', 'Mathias', 'De Roover', 'ba3bd11317de5fb35b62c3400e026c79e76c193f8e317928a4bd6799056d63dfdb347781039c1a4daa8585249dd211af48e29d00b6560d72fd56e55f2a6f5174', 'lokeren', 'Bokslaarstraat 45', 'no.jpg', NULL),
(2, NULL, 'test Company', 'test@chester.com', NULL, 'Mathias', 'De Roover', 'ba3bd11317de5fb35b62c3400e026c79e76c193f8e317928a4bd6799056d63dfdb347781039c1a4daa8585249dd211af48e29d00b6560d72fd56e55f2a6f5174', NULL, NULL, 'no.jpg', NULL),
(3, NULL, 'Jazzy', 'jasper.van.audenaerde@gmail.com', NULL, 'Jasper', 'Van Audenaerde', 'ba3bd11317de5fb35b62c3400e026c79e76c193f8e317928a4bd6799056d63dfdb347781039c1a4daa8585249dd211af48e29d00b6560d72fd56e55f2a6f5174', NULL, NULL, 'no.jpg', NULL),
(4, NULL, 'Immo Glenn', 'bettens.glenn@gmail.com', NULL, 'Glenn', 'Bettens', 'ba3bd11317de5fb35b62c3400e026c79e76c193f8e317928a4bd6799056d63dfdb347781039c1a4daa8585249dd211af48e29d00b6560d72fd56e55f2a6f5174', NULL, NULL, 'no.jpg', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Gegevens worden uitgevoerd voor tabel `vastgoed`
--

INSERT INTO `vastgoed` (`Vastgoed_id`, `Makelaar_id`, `Prijs`, `Locatie`, `Provincie_id`, `Straat`, `Vastgoedtype_id`, `Bebouwingtype_id`, `Status_id`, `Aantal_slaapkamers`, `Aantal_kamers`, `Oppervlakte`, `Garage`, `Bouwjaar`, `Beschrijving`) VALUES
(6, 2, 50, 'aezzezaezea', 3, 'bokslaarstraat 45', 1, NULL, 4, 2, 10, 250, 0, 1977, '<p>beschrijving</p>'),
(11, 1, 180000, 'Brussel', 23, 'ChaussÃ©e?d''Anvers 479', 1, 3, 1, 2, 8, 125, 1, 2013, '<p><span style="color: #4d4d4d; font-family: Arial, Helvetica, sans-serif; font-size: 12px; background-color: #e3eaf4;">Charmante et petite maison de ville de 2 chambres ou maison de 3 studios &agrave; rapport offrant un rapport potentiel de 1200&euro;/mois. A proximit&eacute; des commerces, transports en commun, gare du Nord et axes autoroutiers. Compos&eacute;e: au rdc d''une cuisine super-&eacute;quip&eacute;e, au 1er d''un s&eacute;jour avec plancher cir&eacute;, au 2&egrave;me d''une chambre de 25m&sup2;, et 3&egrave;me chambre (16m&sup2;) avec salle de douche et wc, et sous-sol avec salle de bain, wc, lavabo et buanderie . Enti&egrave;rement r&eacute;nov&eacute;e en 2005 et en tr&egrave;s bon &eacute;tat. Conviendrait parfaitement &agrave; un jeune couple ou jeunes &eacute;tudiants en collocation. Belle opportunit&eacute;. Prix 190000 Infos et visites: 0486335140 Agences s abstenir svp...</span></p>'),
(13, 1, 220000, 'Brussel', 23, 'rue de fuente', 1, 3, 1, 5, 12, 190, 1, 1920, '<p><span style="color: #4d4d4d; font-family: Arial, Helvetica, sans-serif; font-size: 12px; background-color: #e3eaf4;">En plein centre ville situ&eacute; proche de place Anneessens, BENSON REAL ESTATE vous propose une maison d''habitation avec 5 studios comprenant chacune : 1 pi&egrave;ce avec kitchinette, douche et wc - double vitrage. D''une superficie totale de +/- 190 m&sup2;. Id&eacute;ale pour investissement.... Rentabilit&eacute; locative 2.300 &euro;</span></p>'),
(14, 1, 440000, 'Brussel', 23, 'rue de l''avion', 1, 3, 1, 3, 10, 160, 1, 2013, '<p><span style="background-color: #e3eaf4; color: #4d4d4d; font-family: Arial, Helvetica, sans-serif; font-size: 12px;">Trois chambres (137m&sup2;) avec cour (21 m&sup2;) enti&egrave;rement r&eacute;nov&eacute;e (travaux en cours), avec cuisine super &eacute;quip&eacute;e, 2 nouvelles salle de bain.</span></p>'),
(15, 1, 2500, 'Gent', 3, 'Poenixstraat 11', 1, 3, 2, 6, 15, 200, 0, 1966, '<div style="margin: 0px; padding: 0px; color: #4d4d4d; font-family: Arial, Helvetica, sans-serif; font-size: 12px;" align="left">INTERESSANTE OPBRENGSTEIGENDOM IN HET CENTRUM VAN GENT. Op het gelijkvloers een handelspand van +/- 200m&sup2;. Op de eerste verdieping 2 appartementen. Op de tweede verdieping 2 appartementen. Op de derde verdieping 2 studio''s. Zeer goede maandelijkse opbrengst, minimum 4% rente. Prijs en adres op aanvraag. Voor meer inlichtingen neem contact op met Marianne van Riant, partner in vastgoed, op het nummer 0474-71 64 29. Ons volledig aanbod kan u bekijken op www.riantimmo.be.</div><p>&nbsp;</p>'),
(16, 1, 14400, 'Gent', 3, 'Pantserschipstraat.155', 5, 3, 2, NULL, NULL, 400, 1, 2013, '<p><span style="color: #4d4d4d; font-family: Arial, Helvetica, sans-serif; font-size: 12px; background-color: #e3eaf4;">Ge&iuml;soleerd gebouw Automatische sectionaalpoort Parkeerplaats Gas / water / elektriciteit Goed gelegen (R4, begin Gentse haven, ...) Mogelijkheid tot bureau/kantoor Mogelijkheid tot sanitaire voorzieningen (toilet/douche/...) Mooie omgeving</span></p>'),
(17, 1, 80, 'Antwerpen', 1, 'Happaertstraat 10', 6, 3, 2, NULL, NULL, 4, 1, 2013, '<p><span style="color: #4d4d4d; font-family: Arial, Helvetica, sans-serif; font-size: 12px; background-color: #e3eaf4;">Binnenstaanplaats te huur voor 80 euro per maand zonder extra kosten, in een afgesloten parkeergebouw. De staanplaats is gelegen op een toplocatie tussen de Nationalestraat en de Kammenstraat te centrum Antwerpen. Voorwaarden: 3 maanden borg bij Korfina 25 euro registratiekost</span></p>'),
(18, 1, 1100000, 'Brugge', 5, 'Sint-Niklaasstraat 3', 1, 3, 1, 3, 10, 380, 1, 1983, '<p><span style="color: #333333; font-family: Arial, sans-serif; font-size: 12px; line-height: 16px;">Schitterende burgerswoning met trapgevel en met ruime garage (plaats voor 3 wagens), gelegen in de oude kern van Brugge in een gekasseide straat die loopt van de Steenstraat naar de Oude Burg. Dit voormalig woonhuis "Gouden Sleutel" horend bij een winkelhuis aan de Steenstraat biedt u veel ruimte waar u een eigen karakter kan aangeven. De lift zorgt voor een unieke uitstraling. Gelijkvloers (250m&sup2;): Inkomhal (23m&sup2;) afgewerkt met natuursteen, lift, trap naar de eerste verdieping en toegang naar garage en kelder - vestiaire (11m&sup2;) met ingemaakte kast - garage (105m&sup2;) voor 3 wagens met poort, aansluiting nutsvoorzieningen en ramen die zorgen voor de nodige lichtinval - opslagkamer (35m&sup2;) afgewerkt met keramische vloer en ingemaakte kasten - kelder (130m&sup2;) opgedeeld in 2 ruimtes (92m&sup2; + 38m&sup2;). Eerste verdieping (250m&sup2;): hal (58m&sup2;) afgewerkt in zwarte natuursteen, trap naar tweede verdieping en toegang naar de keuken en leefruimte - berging (11m&sup2;) afgewerkt in parket met lavabo, toilet en aansluiting voor wasmachine en droogkast - leefkeuken (43m&sup2;) afgewerkt met parket en vloer, ingemaakte keuken met keramisch fornuis, friteuse, AGA-fornuis op steenkolen, oven, dubbele spoelbak en veel kastruimte - leefruimte (62m&sup2;) afgewerkt met natuursteen, houtkachel en veel lichtinval - kamer (24m&sup2;) aansluitend aan de keuken - zonnig terras (25m&sup2;) afgewerkt met blauwe hardsteen toegankelijk via een schuifraam uit de keuken en kamer. Tweede verdieping (250m&sup2;): nachthal (48m&sup2;) met lift afgewerkt in zwarte natuursteen - berging (15m&sup2;) met vaste vloerbekleding en ingemaakte kast - badkamer (13m&sup2;) afgewerkt met tegels, dubbele lavabo, ligbad en inloopdouche - 2 slaapkamers: kamer 1 (70m&sup2;) afgewerkt in natuursteen, mogelijkheid om een trap te maken naar de zolderverdieping - kamer 2 (17,5m&sup2;) met vaste vloerbekleding - badkamer (12m&sup2;) met dubbele lavabo, ligbad, douche, toilet, toegankelijk via slaapkamer en nachthal - terras (25m&sup2;) met een uniek zicht op de torens van Brugge, afgewerkt in teak met trap naar het platte dak.</span></p>'),
(19, 1, 800, 'Gent', 3, 'Frans Ackermanstraat 22', 4, 3, 2, NULL, NULL, 60, 1, 2013, '<p><span style="color: #333333; font-family: Arial, sans-serif; font-size: 12px; line-height: 16px;">Grote duplexstudio op vierde verdieping (lift aanwezig), volledig bemeubeld te huur (keuken, badkamer, zetel, tafel, stoelen, bureau, bed, nachtkastje,...) prijs inclusief kosten 800 euro/maand, stel gerust vragen, en bel voor een bezoekje Waarborg &euro; 800 Welke kosten zijn inbegrepen? Alles in: televisie (digitaal), internet, gwe &amp; syndicale kosten (elektriciteit gelimiteerd tot 3500 kw/jaar) Contractduur September 2013 - augustus 2014 Vooral interessant voor koppel, aangezien er geen afgesloten ruimtes zijn, behalve de badkamer en het aanwezige bed is een tweepersoonsbed.</span></p>'),
(20, 1, 115000, 'Boninne', 10, '/', 3, 3, 1, NULL, NULL, 2000, 1, 2013, '<p><span style="color: #454648; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 18px;">Dans un village campagnard tr&egrave;s calme mais &agrave; quelques minutes de Namur et de l''E411, un superbe terrain de +/- 20 ares avec une fa&ccedil;ade &agrave; rue de +/- 20 m. Le terrain comprend actuellement deux terrains de tennis pouvant &ecirc;tre conserv&eacute;s en construisant une habitation unifamiliale. Id&eacute;al pour amateur de sport ! Commerces et &eacute;coles &agrave; proximit&eacute; imm&eacute;diate. Belle opportunit&eacute; ! Plus d''informations sur demande au 081/72.36.50.</span></p>'),
(21, 1, 160000, 'Namen', 10, 'place de marchÃ©', 1, 3, 1, 2, 8, 85, 1, 1967, '<p><span style="color: #454648; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 18px;">Id&eacute;alement situ&eacute; en plein centre de Namur (prox magasins), un appartement au 1er &eacute;tage de 90m&sup2; comprenant : hall d''entr&eacute;e, WC ind&eacute;pendant, living, cuisine &eacute;quip&eacute;e, 2 chambres et salle de bain. Nouvelle chaudi&egrave;re individuelle au gaz de ville, double vitrage. Excellente opportunit&eacute;. A saisir !</span></p>'),
(22, 1, 175000, 'Westende', 5, 'Bassevillestraat 79', 1, 3, 1, 2, 10, 77, 1, 1992, '<div class="col-info-description" style="clear: both; min-height: 150px; color: #333333; font-family: Arial, sans-serif; font-size: 12px; line-height: 16px;"><p style="margin: 0px 0px 10px;">LOMBARDSIJDE - Domein FLANDERS GOLF : in een domein gelegen in woongebied (permanente bewoning mogelijk), rustig gelegen en gemeubelde rijwoning, bestaande uit : living met zonnig terras, ingerichte wandkeuken,&nbsp;1 zeer ruime slaapkamer beneden met&nbsp;terras en badkamer (met douche en toilet). op het verdiep : 1 slaapkamer, 1 slaaphoek, badkamer met ligbad en apart toilet. CV op gas, tuinhuis en gemeenschappelijke parking en berging. Kleine beschrijf mogelijk !! Een afspraak of informatie nodig ? Bel 059.300.623. of 0475.641.652.</p></div>'),
(23, 1, 290000, 'Gent', 3, 'Slachthuisstraat 114-116', 1, 3, 1, 4, 15, 248, 1, 1970, '<div class="col-info-description" style="clear: both; min-height: 150px; color: #333333; font-family: Arial, sans-serif; font-size: 12px; line-height: 16px;"><p style="margin: 0px 0px 10px;">Heerlijk ruime en zonnige, te renoveren woning nabij het water in de populaire buurt "de visserij". Dit multifunctionele pand op deze fantastische locatie is momenteel ingericht als gekende handelszaak met woonst maar heeft nog vele andere gezichten. Voordien bestond dit unieke vastgoed uit twee verschillende woningen; thans kan het mits de nodige opfrissing verder worden opgewaardeerd tot zeer aangename, grote gezinswoning. Door zijn vele ruime en lichtrijke vertrekken kan dit pand ook perfect dienen als woon-werkcombinatie, als opbrengsteigendom of als woning voor studerende kinderen. Kortom: een zeer gunstige investering in een gezellige buurt op wandelafstand van centrum, station en binnenring. Absolute aanrader!</p></div>');

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
  ADD CONSTRAINT `fk_Vastgoed_Bebouwing_types1` FOREIGN KEY (`Bebouwingtype_id`) REFERENCES `bebouwing_types` (`Bebouwingtype_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Makelaars1` FOREIGN KEY (`Makelaar_id`) REFERENCES `makelaars` (`Makelaar_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Provincies1` FOREIGN KEY (`Provincie_id`) REFERENCES `provincies` (`Provincie_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Status1` FOREIGN KEY (`Status_id`) REFERENCES `status` (`Status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Vastgoed_Woningtypes1` FOREIGN KEY (`Vastgoedtype_id`) REFERENCES `vastgoedtypes` (`Vastgoedtype_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
