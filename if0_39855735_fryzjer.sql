-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql310.infinityfree.com
-- Czas generowania: 26 Maj 2026, 02:44
-- Wersja serwera: 11.4.11-MariaDB
-- Wersja PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `if0_39855735_fryzjer`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `days_off`
--

CREATE TABLE `days_off` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `days_off`
--

INSERT INTO `days_off` (`id`, `date`, `created_at`) VALUES
(1, '2026-04-29', '2026-04-27 15:47:18'),
(2, '2026-04-30', '2026-04-27 18:33:49'),
(3, '2026-04-26', '2026-04-27 18:44:15'),
(4, '2026-05-03', '2026-04-27 18:57:46'),
(5, '2026-05-26', '2026-05-25 21:46:04'),
(6, '2026-05-27', '2026-05-26 06:11:32');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` time NOT NULL,
  `duration` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `service_type` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `date`, `hour`, `duration`, `created_at`, `service_type`) VALUES
(6, 3, '2026-04-27', '10:30:00', 120, '2026-04-27 19:12:21', ''),
(19, 5, '2026-05-28', '11:00:00', 60, '2026-05-26 06:29:50', 'style'),
(11, 6, '2026-05-11', '11:00:00', 30, '2026-05-12 06:59:02', 'cut'),
(12, 6, '2026-05-19', '14:30:00', 30, '2026-05-18 21:25:01', 'cut'),
(17, 6, '2026-05-31', '09:00:00', 30, '2026-05-26 06:07:17', 'cut');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `verify_hash` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `reset_hash` varchar(255) DEFAULT NULL,
  `reset_hash_expires` datetime DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `email`, `gender`, `role`, `password_hash`, `created_at`, `verify_hash`, `verified`, `reset_hash`, `reset_hash_expires`, `reset_expires`) VALUES
(5, '621piotrek06379@gmail.com', 'male', 'client', '$2y$10$3MIBt55csEWlzuSLnwJcuOpORQrC5HxdTHZ6QyenNy6BzBDELCrDK', '2026-04-27 20:02:17', NULL, 1, NULL, NULL, '2026-04-27 18:20:57'),
(6, 'pitivvpiotr@gmail.com', 'male', 'admin', '$2y$10$7wtn0.s8ZjHhNi3In4k8w..00HlJ0ZQVjksIjpKmfHvUaralmmJ/y', '2026-04-27 20:05:22', NULL, 1, NULL, NULL, '2026-05-12 03:26:47'),
(8, 'test@gmail.com', 'female', 'client', '$2y$10$7YmIEKSgH9uEmyBfwK.JGuY.M5gVx5.oGTuuAIRSTH1WL4TftWKa6', '2026-04-27 21:56:16', NULL, 1, NULL, NULL, NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `days_off`
--
ALTER TABLE `days_off`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`);

--
-- Indeksy dla tabeli `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `days_off`
--
ALTER TABLE `days_off`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
