-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 14 Αυγ 2014 στις 21:41:00
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

--
-- Άδειασμα δεδομένων του πίνακα `users`
--

INSERT INTO `users` (`username`, `name`, `userType`, `password`) VALUES
('user1', 'User One', 'advanced', '24c9e15e52afc47c225b757e7bee1f9d'),
('user2', 'User Two', 'registered', '7e58d63b60197ceb55a1c487989a3720'),
('user3', 'User Three', 'registered', '92877af70a45fd6a2ed7fe81e1236b78'),
('user4', 'user4', 'advanced', '3f02ebe3d7929b091e3d8ccfde2f3bc6');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
