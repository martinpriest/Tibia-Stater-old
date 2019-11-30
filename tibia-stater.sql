-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 30 Lis 2019, 07:11
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
-- Baza danych: `tibia-stater`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `highscore_categories`
--

CREATE TABLE `highscore_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `highscore_transaction`
--

CREATE TABLE `highscore_transaction` (
  `id` int(11) NOT NULL,
  `idTime` int(11) NOT NULL,
  `idWorld` int(11) NOT NULL,
  `idPlayer` int(11) NOT NULL,
  `idVocation` int(11) NOT NULL,
  `idHighscoreCategory` int(11) NOT NULL,
  `rankPosition` int(11) NOT NULL,
  `rankValue` bigint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `idWorld` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `idVocation` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `sex` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `players_transaction`
--

CREATE TABLE `players_transaction` (
  `id` int(11) NOT NULL,
  `idTime` int(11) NOT NULL,
  `idWorld` int(11) NOT NULL,
  `idPlayer` int(11) NOT NULL,
  `idResidence` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `timeOnline` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_name_history`
--

CREATE TABLE `player_name_history` (
  `id` int(11) NOT NULL,
  `idTime` int(11) NOT NULL,
  `idPlayer` int(11) NOT NULL,
  `formerName` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_sex_history`
--

CREATE TABLE `player_sex_history` (
  `id` int(11) NOT NULL,
  `idTime` int(11) NOT NULL,
  `idPlayer` int(11) NOT NULL,
  `sex` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_world_history`
--

CREATE TABLE `player_world_history` (
  `id` int(11) NOT NULL,
  `idTime` int(11) NOT NULL,
  `idPlayer` int(11) NOT NULL,
  `idFormerWorld` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `residences`
--

CREATE TABLE `residences` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `time`
--

CREATE TABLE `time` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `dayOfMonth` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `vocations`
--

CREATE TABLE `vocations` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `worlds`
--

CREATE TABLE `worlds` (
  `id` int(11) NOT NULL,
  `name` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `idLocation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `world_locations`
--

CREATE TABLE `world_locations` (
  `id` int(11) NOT NULL,
  `location` varchar(32) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `highscore_categories`
--
ALTER TABLE `highscore_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `highscore_transaction`
--
ALTER TABLE `highscore_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPlayer` (`idPlayer`),
  ADD KEY `idHighscoreCategory` (`idHighscoreCategory`),
  ADD KEY `idTime` (`idTime`),
  ADD KEY `idWorld` (`idWorld`),
  ADD KEY `idVocation` (`idVocation`);

--
-- Indeksy dla tabeli `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sex` (`sex`),
  ADD KEY `idVocation` (`idVocation`),
  ADD KEY `idWorld` (`idWorld`);

--
-- Indeksy dla tabeli `players_transaction`
--
ALTER TABLE `players_transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTime` (`idTime`),
  ADD KEY `idWorld` (`idWorld`),
  ADD KEY `idPlayer` (`idPlayer`),
  ADD KEY `idResidence` (`idResidence`);

--
-- Indeksy dla tabeli `player_name_history`
--
ALTER TABLE `player_name_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idTime` (`idTime`),
  ADD KEY `idPlayer` (`idPlayer`);

--
-- Indeksy dla tabeli `player_sex_history`
--
ALTER TABLE `player_sex_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPlayer` (`idPlayer`),
  ADD KEY `idTime` (`idTime`);

--
-- Indeksy dla tabeli `player_world_history`
--
ALTER TABLE `player_world_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idTime` (`idTime`),
  ADD KEY `idPlayer` (`idPlayer`),
  ADD KEY `idFormerWorld` (`idFormerWorld`);

--
-- Indeksy dla tabeli `residences`
--
ALTER TABLE `residences`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `time`
--
ALTER TABLE `time`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `vocations`
--
ALTER TABLE `vocations`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `worlds`
--
ALTER TABLE `worlds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idLocation` (`idLocation`);

--
-- Indeksy dla tabeli `world_locations`
--
ALTER TABLE `world_locations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `highscore_categories`
--
ALTER TABLE `highscore_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `highscore_transaction`
--
ALTER TABLE `highscore_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `players_transaction`
--
ALTER TABLE `players_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `player_name_history`
--
ALTER TABLE `player_name_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `player_sex_history`
--
ALTER TABLE `player_sex_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `player_world_history`
--
ALTER TABLE `player_world_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `residences`
--
ALTER TABLE `residences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `time`
--
ALTER TABLE `time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `vocations`
--
ALTER TABLE `vocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `worlds`
--
ALTER TABLE `worlds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `world_locations`
--
ALTER TABLE `world_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `highscore_transaction`
--
ALTER TABLE `highscore_transaction`
  ADD CONSTRAINT `highscore_transaction_ibfk_1` FOREIGN KEY (`idHighscoreCategory`) REFERENCES `highscore_categories` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `highscore_transaction_ibfk_2` FOREIGN KEY (`idTime`) REFERENCES `time` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `highscore_transaction_ibfk_3` FOREIGN KEY (`idWorld`) REFERENCES `worlds` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `highscore_transaction_ibfk_4` FOREIGN KEY (`idPlayer`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `highscore_transaction_ibfk_5` FOREIGN KEY (`idVocation`) REFERENCES `vocations` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `players_transaction`
--
ALTER TABLE `players_transaction`
  ADD CONSTRAINT `players_transaction_ibfk_1` FOREIGN KEY (`idPlayer`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `players_transaction_ibfk_2` FOREIGN KEY (`idWorld`) REFERENCES `worlds` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `players_transaction_ibfk_3` FOREIGN KEY (`idTime`) REFERENCES `time` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `players_transaction_ibfk_4` FOREIGN KEY (`idResidence`) REFERENCES `residences` (`id`);

--
-- Ograniczenia dla tabeli `player_name_history`
--
ALTER TABLE `player_name_history`
  ADD CONSTRAINT `player_name_history_ibfk_1` FOREIGN KEY (`idPlayer`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `player_sex_history`
--
ALTER TABLE `player_sex_history`
  ADD CONSTRAINT `player_sex_history_ibfk_1` FOREIGN KEY (`idPlayer`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `player_world_history`
--
ALTER TABLE `player_world_history`
  ADD CONSTRAINT `player_world_history_ibfk_1` FOREIGN KEY (`idPlayer`) REFERENCES `players` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `player_world_history_ibfk_2` FOREIGN KEY (`idFormerWorld`) REFERENCES `worlds` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `worlds`
--
ALTER TABLE `worlds`
  ADD CONSTRAINT `worlds_ibfk_1` FOREIGN KEY (`idLocation`) REFERENCES `world_locations` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
