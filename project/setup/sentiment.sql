-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 14 Αυγ 2014 στις 19:14:54
-- Έκδοση Διακομιστή: 5.5.38
-- Έκδοση PHP: 5.3.10-1ubuntu3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Βάση: `sentiment`
--

-- --------------------------------------------------------

--
-- Δομή Πίνακα για τον Πίνακα `adjectives`
--

CREATE TABLE IF NOT EXISTS `adjectives` (
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adjective` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `definition` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Δομή Πίνακα για τον Πίνακα `contactus`
--

CREATE TABLE IF NOT EXISTS `contactus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Δομή Πίνακα για τον Πίνακα `examples`
--

CREATE TABLE IF NOT EXISTS `examples` (
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `example` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Δομή Πίνακα για τον Πίνακα `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `value` tinyint(4) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Δομή Πίνακα για τον Πίνακα `rankings`
--

CREATE TABLE IF NOT EXISTS `rankings` (
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rank` tinyint(11) NOT NULL,
  PRIMARY KEY (`code`,`username`),
  KEY `username` (`username`),
  KEY `rank` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Δομή Πίνακα για τον Πίνακα `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userType` enum('registered','advanced','','') COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `examples`
--
ALTER TABLE `examples`
  ADD CONSTRAINT `examples_ibfk_2` FOREIGN KEY (`code`) REFERENCES `adjectives` (`code`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Περιορισμοί για πίνακα `rankings`
--
ALTER TABLE `rankings`
  ADD CONSTRAINT `rankings_ibfk_3` FOREIGN KEY (`code`) REFERENCES `adjectives` (`code`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `rankings_ibfk_4` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `rankings_ibfk_5` FOREIGN KEY (`rank`) REFERENCES `rank` (`value`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
