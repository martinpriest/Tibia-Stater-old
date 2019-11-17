-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 17 Lis 2019, 07:07
-- Wersja serwera: 10.1.38-MariaDB
-- Wersja PHP: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `meta-tibia-stater`
--
CREATE DATABASE IF NOT EXISTS `meta-tibia-stater` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `meta-tibia-stater`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `data_sources`
--

CREATE TABLE `data_sources` (
  `id` int(11) NOT NULL,
  `link` varchar(512) NOT NULL,
  `name` varchar(64) NOT NULL,
  `charset` varchar(32) NOT NULL,
  `comment` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `data_warehouse_summary`
--

CREATE TABLE `data_warehouse_summary` (
  `id` int(11) NOT NULL,
  `extractProcess` int(11) NOT NULL DEFAULT '0',
  `transformProcess` int(11) NOT NULL DEFAULT '0',
  `loadProcess` int(11) NOT NULL DEFAULT '0',
  `selectProcess` int(11) NOT NULL DEFAULT '0',
  `clearingProcess` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `extract_history`
--

CREATE TABLE `extract_history` (
  `id` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idWorld` int(11) NOT NULL,
  `fileDownloaded` int(11) NOT NULL,
  `executionTime` float NOT NULL,
  `onlinePlayers` tinyint(1) NOT NULL,
  `highscores` tinyint(1) NOT NULL,
  `guilds` tinyint(1) NOT NULL,
  `operationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `load_history`
--

CREATE TABLE `load_history` (
  `id` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `recordsInserted` int(11) NOT NULL,
  `recordsUpdated` int(11) NOT NULL,
  `executionTime` float NOT NULL,
  `operationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `meta_information`
--

CREATE TABLE `meta_information` (
  `id` int(11) NOT NULL,
  `dataWarehouseName` varchar(32) NOT NULL,
  `lastOperation` int(11) NOT NULL,
  `description` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `scheduler`
--

CREATE TABLE `scheduler` (
  `id` int(11) NOT NULL,
  `idWorld` int(11) NOT NULL,
  `onlinePlayers` tinyint(1) NOT NULL DEFAULT '0',
  `highscores` tinyint(1) NOT NULL DEFAULT '0',
  `guilds` tinyint(1) NOT NULL DEFAULT '0',
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `transform_history`
--

CREATE TABLE `transform_history` (
  `id` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `fileParsed` int(11) NOT NULL,
  `executionTime` float NOT NULL,
  `operationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(32) NOT NULL,
  `password` varchar(128) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_history`
--

CREATE TABLE `user_history` (
  `id` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `worlds`
--

CREATE TABLE `worlds` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `location` varchar(32) DEFAULT NULL,
  `pvpType` varchar(32) DEFAULT NULL,
  `onlinePlayersReady` tinyint(1) NOT NULL DEFAULT '0',
  `highscoresReady` tinyint(1) NOT NULL DEFAULT '0',
  `guildsReady` tinyint(1) NOT NULL DEFAULT '0',
  `lastOperation` int(11) NOT NULL,
  `lastLoadDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `data_sources`
--
ALTER TABLE `data_sources`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `data_warehouse_summary`
--
ALTER TABLE `data_warehouse_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `extract_history`
--
ALTER TABLE `extract_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idWorld` (`idWorld`);

--
-- Indeksy dla tabeli `load_history`
--
ALTER TABLE `load_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUser` (`idUser`);

--
-- Indeksy dla tabeli `meta_information`
--
ALTER TABLE `meta_information`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `scheduler`
--
ALTER TABLE `scheduler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idWorld` (`idWorld`);

--
-- Indeksy dla tabeli `transform_history`
--
ALTER TABLE `transform_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUser` (`idUser`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `user_history`
--
ALTER TABLE `user_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUser` (`idUser`);

--
-- Indeksy dla tabeli `worlds`
--
ALTER TABLE `worlds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `data_sources`
--
ALTER TABLE `data_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `data_warehouse_summary`
--
ALTER TABLE `data_warehouse_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `extract_history`
--
ALTER TABLE `extract_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `load_history`
--
ALTER TABLE `load_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `meta_information`
--
ALTER TABLE `meta_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `scheduler`
--
ALTER TABLE `scheduler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `transform_history`
--
ALTER TABLE `transform_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `user_history`
--
ALTER TABLE `user_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `worlds`
--
ALTER TABLE `worlds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `extract_history`
--
ALTER TABLE `extract_history`
  ADD CONSTRAINT `extract_history_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `extract_history_ibfk_2` FOREIGN KEY (`idWorld`) REFERENCES `worlds` (`id`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `load_history`
--
ALTER TABLE `load_history`
  ADD CONSTRAINT `load_history_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `scheduler`
--
ALTER TABLE `scheduler`
  ADD CONSTRAINT `scheduler_ibfk_1` FOREIGN KEY (`idWorld`) REFERENCES `worlds` (`id`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `transform_history`
--
ALTER TABLE `transform_history`
  ADD CONSTRAINT `transform_history_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `user_history`
--
ALTER TABLE `user_history`
  ADD CONSTRAINT `user_history_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
