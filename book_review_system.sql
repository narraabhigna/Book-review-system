-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 24, 2024 at 05:04 AM
-- Server version: 8.0.29
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book_review_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `Book_id` int NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `Author` varchar(255) NOT NULL,
  PRIMARY KEY (`Book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`Book_id`, `Title`, `Author`) VALUES
(1, 'The Midnight Garden', 'Evelyn Marlowe'),
(2, 'Echoes of Eternity', 'Benjamin Black'),
(3, 'The Lost City', 'Charlotte Rivers'),
(4, 'Shadow of the Moon', 'Alexander Wells'),
(5, 'Whispers in the Wind', 'Olivia Harper'),
(6, 'The Secret Diary', 'James Turner'),
(7, 'Beyond the Stars', 'Isabella Rose'),
(8, 'The Forgotten Kingdom', 'Sophia Bennett'),
(9, 'Into the Abyss', 'David Mitchell'),
(10, 'The Hidden Chamber', 'Amelia Scott'),
(11, 'Under the Silver Moon', 'Gabriel Knight'),
(12, 'Voices from the Past', 'Lily Anderson'),
(13, 'The Enchanted Forest', 'Matthew Harrison'),
(14, 'Beneath the Surface', 'Emily Foster'),
(15, 'The Crystal Key', 'Daniel Carter'),
(16, 'Shadows in the Mist', 'Victoria Reed'),
(17, 'Lost and Found', 'Samuel Taylor'),
(18, 'The Emerald Isle', 'Penelope Moore'),
(19, 'Echoes of the Past', 'Chloe Johnson'),
(20, 'The Secret Garden', 'Frances Hodgson Burnett');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `Review_id` int NOT NULL AUTO_INCREMENT,
  `User_id` varchar(50) NOT NULL,
  `Book_id` int NOT NULL,
  `Rating` int NOT NULL,
  `Review` text NOT NULL,
  PRIMARY KEY (`Review_id`),
  KEY `fk_user_id` (`User_id`),
  KEY `fk_book_id` (`Book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `User_id` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY (`User_id`),
  UNIQUE KEY `UNIQUE` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_book_id` FOREIGN KEY (`Book_id`) REFERENCES `books` (`Book_id`),
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`User_id`) REFERENCES `user` (`User_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
