-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 04:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinematch_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `daftar_film`
--

CREATE TABLE `daftar_film` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `tahun` int(4) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `usia` varchar(10) NOT NULL DEFAULT 'SU',
  `poster` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daftar_film`
--

INSERT INTO `daftar_film` (`id`, `judul`, `genre`, `tahun`, `rating`, `usia`, `poster`) VALUES
(1, 'Extraction 2', 'ACTION', 2023, 7.9, '17+', 'https://media.suara.com/pictures/original/2023/06/16/39402-sinopsis-extraction-2.jpg'),
(2, 'John Wick: Chapter 4', 'ACTION', 2023, 8.1, '17+', 'https://media-cache.cinematerial.com/p/500x/6dlcsiyn/john-wick-chapter-4-british-movie-poster.jpg?v=1686035891'),
(3, 'Top Gun: Maverick', 'ACTION', 2022, 8.3, '13+', 'https://m.media-amazon.com/images/I/71BokibfVUL.jpg'),
(4, 'The Raid 2 : Berandal', 'ACTION', 2014, 8.0, '21+', 'https://media-cache.cinematerial.com/p/500x/iref9p02/the-raid-2-berandal-movie-poster.jpg?v=1456436127'),
(5, 'Interstellar', 'SCI-FI', 2014, 8.7, '13+', 'https://m.media-amazon.com/images/I/614QSV5M4lL._AC_UF894,1000_QL80_.jpg'),
(6, 'Inception', 'SCI-FI', 2010, 8.8, '13+', 'https://image.tmdb.org/t/p/original/xlaY2zyzMfkhk0HSC5VUwzoZPU1.jpg'),
(7, 'The Matrix Resurrections', 'SCI-FI', 2021, 7.2, '17+', 'https://play-lh.googleusercontent.com/H6Ioa-dJbdily7_7kbL6ZLPaZXpm8T5fOfTAwZYa7UXjtR30ekNWD2ZHl2HwI_WcT0E-D7ZZ4uxcJfuvwTDw'),
(8, 'Avatar: The Way of Water', 'SCI-FI', 2022, 7.6, 'SU', 'https://upload.wikimedia.org/wikipedia/id/5/54/Avatar_The_Way_of_Water_poster.jpg'),
(9, 'The Conjuring 3', 'HORROR', 2021, 7.5, '17+', 'https://cdn.teater.co/imgs/the-conjuring-3-2020-0_600_880.webp'),
(10, 'Insidious: The Red Door', 'HORROR', 2023, 6.8, '13+', 'https://upload.wikimedia.org/wikipedia/id/thumb/4/4f/Insidious_the_red_door.png/250px-Insidious_the_red_door.png'),
(11, 'Pengabdi Setan 2', 'HORROR', 2022, 7.8, '17+', 'https://i.pinimg.com/736x/ec/1e/b2/ec1eb29ae4d0617a580f63f5d65e0494.jpg'),
(12, 'Toy Story 4', 'ANIMATION', 2019, 7.7, 'SU', 'https://image.tmdb.org/t/p/original/w9kR8qbmQ01HwnvK4alvnQ2ca0L.jpg'),
(13, 'Coco', 'ANIMATION', 2017, 8.4, 'SU', 'https://upload.wikimedia.org/wikipedia/id/9/98/Coco_%282017_film%29_poster.jpg'),
(15, 'La La Land', 'ROMANCE', 2016, 8.0, 'SU', 'https://i.ebayimg.com/images/g/-5cAAOSwaZRldgAc/s-l1200.jpg'),
(16, 'Ada Apa Dengan Cinta? 2', 'ROMANCE', 2016, 7.4, '13+', 'https://upload.wikimedia.org/wikipedia/id/6/60/Ada_Apa_Denga_Cinta_2.jpg'),
(17, 'Cars 3', 'Animation', 2017, 8.0, 'SU', 'https://m.media-amazon.com/images/I/91SHTktZCdL._AC_UF894,1000_QL80_.jpg'),
(18, 'Upin & Ipin : Keris Siamang Tunggal', 'Animation', 2026, 8.4, 'SU', 'https://m.media-amazon.com/images/M/MV5BMDcwYjE5NjktYjU4Yy00N2ZhLWEwNTEtNGMwZTQ1MTY3ZDE4XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daftar_film`
--
ALTER TABLE `daftar_film`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daftar_film`
--
ALTER TABLE `daftar_film`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
