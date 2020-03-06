-- TO DO: DATA STRUCTURE AND PL/SQL FOR:
-- 1. CREATURE KILL STATS
-- 2. GUILD STATS
-- 3. HOUSE STATS
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET GLOBAL event_scheduler = ON;
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- primary table structure
    SET FOREIGN_KEY_CHECKS = 0;
    DROP TABLE IF EXISTS `player_former_world_fact`;
    DROP TABLE IF EXISTS `world_dim`;
    DROP TABLE IF EXISTS `world_location_dim`;
    DROP TABLE IF EXISTS `vocation_dim`;
    DROP TABLE IF EXISTS `date_dim`;
    DROP TABLE IF EXISTS `residence_dim`;
    DROP TABLE IF EXISTS `player_dim`;
    DROP TABLE IF EXISTS `highscore_category_dim`;
    DROP TABLE IF EXISTS `highscore_fact`;
    DROP TABLE IF EXISTS `player_fact`;
    DROP TABLE IF EXISTS `player_former_name_fact`;
    DROP TABLE IF EXISTS `player_former_sex_fact`;

    -- data clearing procedures
    DROP PROCEDURE IF EXISTS `ClearDB`;
    DROP PROCEDURE IF EXISTS `ClearLastInsert`;
    DROP PROCEDURE IF EXISTS `ClearInsertFromDay`;

    -- profit agregates
    DROP TABLE IF EXISTS `daily_profit`;
    DROP TABLE IF EXISTS `weekly_profit`;
    DROP TABLE IF EXISTS `monthly_profit`;

    DROP FUNCTION IF EXISTS `CountDailyProfitFromSex`;
    DROP FUNCTION IF EXISTS `CountDailyProfitFromName`;
    DROP FUNCTION IF EXISTS `CountDailyProfitFromWorld`;
    DROP PROCEDURE IF EXISTS `InsertDailyProfitData`;
    DROP PROCEDURE IF EXISTS `InsertWeeklyProfitData`;
    DROP PROCEDURE IF EXISTS `InsertMonthlyProfitData`;
    SET FOREIGN_KEY_CHECKS = 1;
-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `world_location_dim`
--

CREATE TABLE IF NOT EXISTS `world_location_dim` (
  `id` TINYINT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `world_dim`
--

CREATE TABLE IF NOT EXISTS `world_dim` (
  `id` TINYINT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) UNIQUE KEY COLLATE utf8_polish_ci NOT NULL,
  `idLocation` TINYINT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idLocation`) REFERENCES world_location_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `vocation_dim`
--

CREATE TABLE IF NOT EXISTS `vocation_dim` (
  `id` TINYINT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `date_dim`
--

CREATE TABLE IF NOT EXISTS `date_dim` (
  `id` SMALLINT(11) NOT NULL AUTO_INCREMENT,
  `year` SMALLINT(11) NOT NULL,
  `month` TINYINT(11) NOT NULL,
  `dayOfMonth` TINYINT(11) NOT NULL,
  `dayOfWeek` TINYINT(11) NOT NULL,
  `fullDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

CREATE TABLE IF NOT EXISTS `time_dim` (
  `id` SMALLINT(11) NOT NULL AUTO_INCREMENT,
  `hour` TINYINT(11) NOT NULL,
  `minute` TINYINT(11) NOT NULL
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `residence_dim`
--

CREATE TABLE IF NOT EXISTS `residence_dim` (
  `id` TINYINT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_dim`
--

CREATE TABLE IF NOT EXISTS `player_dim` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idActualWorld` TINYINT(11) NOT NULL,
  `idVocation` TINYINT(11) NOT NULL,
  `idAddDate` SMALLINT(11) NOT NULL,
  `idUpdateDate` SMALLINT(11) NOT NULL,
  `idActualResidence` TINYINT(11) NOT NULL,
  `name` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL,
  `title` VARCHAR(64) COLLATE utf8_polish_ci NOT NULL,
  `actualSex` BOOLEAN NOT NULL,
  `accountStatus` BOOLEAN NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idVocation`) REFERENCES vocation_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idAddDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idUpdateDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `highscore_category_dim`
--

CREATE TABLE IF NOT EXISTS `highscore_category_dim` (
  `id` TINYINT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `highscore_fact`
--

CREATE TABLE IF NOT EXISTS `highscore_fact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDate` SMALLINT(11) NOT NULL,
  `idWorld` TINYINT(11) NOT NULL,
  `idPlayer` INT(11) NOT NULL,
  `idVocation` TINYINT(11) NOT NULL,
  `idHighscoreCategory` TINYINT(11) NOT NULL,
  `rankPosition` SMALLINT(11) NOT NULL,
  `rankValue` BIGINT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idPlayer`) REFERENCES player_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idVocation`) REFERENCES vocation_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idHighscoreCategory`) REFERENCES highscore_category_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_fact`
--

CREATE TABLE IF NOT EXISTS `player_fact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDate` SMALLINT(11) NOT NULL,
  `idWorld` TINYINT(11) NOT NULL,
  `idPlayer` INT(11) NOT NULL,
  `idResidence` TINYINT(11) NOT NULL,
  `startLevel` INT(11) NOT NULL,
  `endLevel` INT(11) NOT NULL,
  `achievmentPoint` INT(11) NOT NULL,
  `timeOnline` INT(11) NOT NULL,
  `onlineTimeJSON` JSON DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idPlayer`) REFERENCES player_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idResidence`) REFERENCES residence_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_fact`
-- Dodac tabele z agregacjami 

CREATE TABLE IF NOT EXISTS `online_time_fact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDate` SMALLINT(11) NOT NULL,
  `idTime` SMALLINT(11) NOT NULL,
  `idWorld` TINYINT(11) NOT NULL,
  `playersOnline` SMALLINT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idPlayer`) REFERENCES player_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idResidence`) REFERENCES residence_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_former_name_fact`
--

CREATE TABLE IF NOT EXISTS `player_former_name_fact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDate` SMALLINT(11) NOT NULL,
  `idNewWorld` TINYINT(11) NOT NULL,
  `idFormerWorld` TINYINT(11) NOT NULL,
  `idPlayer` INT(11) NOT NULL,
  `formerName` VARCHAR(32) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idNewWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idFormerWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idPlayer`) REFERENCES player_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_former_sex_fact`
--

CREATE TABLE IF NOT EXISTS `player_former_sex_fact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDate` SMALLINT(11) NOT NULL,
  `idWorld` INT(11) NOT NULL,
  `idPlayer` INT(11) NOT NULL,
  `sex` BOOLEAN NOT NULL,
  `changeCost` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idPlayer`) REFERENCES player_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `player_former_world_fact`
--

CREATE TABLE IF NOT EXISTS `player_former_world_fact` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idDate` SMALLINT(11) NOT NULL,
  `idPlayer` INT(11) NOT NULL,
  `idFormerWorld` INT(11) NOT NULL,
  `changeCost` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idPlayer`) REFERENCES player_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idFormerWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


-- ------------------------------------------------------------------------------
-- -----------------------CZYSZCZENIE HURTOWNI DANYCH----------------------------
-- 1. CZYSZCZENIE PELNE----------------------------------------------------------
-- 2. CZYSZCZENIE OSTATNIEGO PROCESU LOAD----------------------------------------
-- 3. CZYSZCZENIE FAKTÓW Z WYBRANEGO DNIA----------------------------------------
-- ------------------------------------------------------------------------------

-- Procedura ClearDB() : czysci wszystkie dane z hurtowni danych
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS ClearDB()
BEGIN
  DELETE FROM `world_location_dim`;
  DELETE FROM `world_dim`;
  DELETE FROM `vocation_dim`;
  DELETE FROM `date_dim`;
  DELETE FROM `residence_dim`;
  DELETE FROM `player_dim`;
  DELETE FROM `highscore_category_dim`;
  DELETE FROM `highscore_fact`;
  DELETE FROM `player_fact`;
  DELETE FROM `player_former_name_fact`;
  DELETE FROM `player_former_sex_fact`;
  DELETE FROM `player_former_world_fact`;

  ALTER TABLE `world_location_dim` auto_increment=1;
  ALTER TABLE `world_dim` auto_increment=1;
  ALTER TABLE `vocation_dim` auto_increment=1;
  ALTER TABLE `date_dim` auto_increment=1;
  ALTER TABLE `residence_dim` auto_increment=1;
  ALTER TABLE `player_dim` auto_increment=1;
  ALTER TABLE `highscore_category_dim` auto_increment=1;
  ALTER TABLE `highscore_fact` auto_increment=1;
  ALTER TABLE `player_fact` auto_increment=1;
  ALTER TABLE `player_former_name_fact` auto_increment=1;
  ALTER TABLE `player_former_sex_fact` auto_increment=1;
  ALTER TABLE `player_former_world_fact` auto_increment=1;
END;
//
DELIMITER ;

-- Procedura ClearLastInsert() : czysci wszystkie dane z ostatniego ładowania
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS ClearLastInsert()
BEGIN
  SELECT MAX(`id`) INTO @lastIdTime FROM `date_dim`;
  DELETE FROM `date_dim` WHERE `id` = @lastIdTime;
  DELETE FROM `highscore_fact` WHERE `idDate` = @lastIdTime;
  DELETE FROM `player_fact` WHERE `idDate` = @lastIdTime;
  DELETE FROM `player_former_name_fact` WHERE `idDate` = @lastIdTime;
  DELETE FROM `player_former_sex_fact` WHERE `idDate` = @lastIdTime;
  DELETE FROM `player_former_world_fact` WHERE `idDate` = @lastIdTime;
END;
//
DELIMITER ;

-- Procedura ClearInsertFromDay() : czysci wszystkie dane z wybranego dnia
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS ClearInsertFromDay(
  IN dayOfMonth INT,
  IN month INT,
  IN year INT
)
BEGIN
  SELECT `id` INTO @idDate FROM `date_dim`
  WHERE `dayOfMonth` = dayOfMonth AND `month` = month AND `year` = year;

  DELETE FROM `date_dim` WHERE `id` = @idDate;
  DELETE FROM `highscore_fact` WHERE `idDate` = @idDate;
  DELETE FROM `player_fact` WHERE `idDate` = @idDate;
  DELETE FROM `player_former_name_fact` WHERE `idDate` = @idDate;
  DELETE FROM `player_former_sex_fact` WHERE `idDate` = @idDate;
  DELETE FROM `player_former_world_fact` WHERE `idDate` = @idDate;
END;
//
DELIMITER ;

-- ------------------------------------------------------------------------------
-- ----------------------------WYLICZANIE ZAROBKÓW-------------------------------
-- 0. TABELE Z AGREGATAMI ZAROBKOW-----------------------------------------------
-- 1. FUNKCJE AGREGUJACE ZAROBKI Z DNIA------------------------------------------
-- 2. PROCEDURY WSTAWIAJACE DANE WYLICZONE Z FUNKCJI-----------------------------
-- 3. EVENT WYWOLUJACY PROCEDURY ------------------------------------------------
-- 4. WIDOKI DLA TABELI AGREGACJI ZYSKOW-----------------------------------------
-- 5. UPRAWNIENIA: FUNKCJE I PROCEDURY: DB // WIDOKI: RANDOM USER----------------
-- ------------------------------------------------------------------------------

-- Table contains daily profit data
CREATE TABLE IF NOT EXISTS daily_profit (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idWorld` TINYINT(11) NOT NULL,
  `idDate` SMALLINT(11) NOT NULL,
  `sexChangeProfit` FLOAT NOT NULL,
  `nameChangeProfit` FLOAT NOT NULL,
  `worldChangeProfit` FLOAT NOT NULL,
  `sumOfProfit` FLOAT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
);

-- Table contains weekly profit data
CREATE TABLE IF NOT EXISTS weekly_profit (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idWorld` TINYINT(11) NOT NULL,
  `idDate` SMALLINT(11) NOT NULL,
  `sexChangeProfit` FLOAT NOT NULL,
  `nameChangeProfit` FLOAT NOT NULL,
  `worldChangeProfit` FLOAT NOT NULL,
  `sumOfProfit` FLOAT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
);

-- Table contains weekly profit data
CREATE TABLE IF NOT EXISTS monthly_profit (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `idWorld` TINYINT(11) NOT NULL,
  `idDate` SMALLINT(11) NOT NULL,
  `sexChangeProfit` FLOAT NOT NULL,
  `nameChangeProfit` FLOAT NOT NULL,
  `worldChangeProfit` FLOAT NOT NULL,
  `sumOfProfit` FLOAT NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idDate`) REFERENCES date_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  FOREIGN KEY (`idWorld`) REFERENCES world_dim(`id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
);

-- Function returns daily profit by world from changing sex
DELIMITER //
CREATE FUNCTION CountDailyProfitFromSex(idWorld INT)
RETURNS FLOAT
DETERMINISTIC
BEGIN
  SELECT MAX(`id`) INTO @lastIdTime FROM `date_dim`;
  SELECT COUNT(`id`) INTO @changeAmount FROM `player_former_sex_fact`
  WHERE `idDate` = @idDate AND `idWorld` = idWorld;
  -- pricing table
  -- SELECT `price`
  RETURN @changeAmount * 30;
END;
//
DELIMITER ;

-- Function returns daily profit by world from changing name
DELIMITER //
CREATE FUNCTION CountDailyProfitFromName(idWorld INT)
RETURNS FLOAT
DETERMINISTIC
BEGIN
  SELECT MAX(`id`) INTO @lastIdTime FROM `date_dim`;
  SELECT COUNT(`id`) INTO @changeAmount FROM `player_former_name_fact`
  WHERE `idDate` = @idDate AND `idWorld` = idWorld;
  -- pricing table
  -- SELECT `price`
  RETURN @changeAmount * 30;
END;
//
DELIMITER ;

-- Function returns daily profit by world from changing world
DELIMITER //
CREATE FUNCTION CountDailyProfitFromWorld(idWorld INT)
RETURNS FLOAT
DETERMINISTIC
BEGIN
  SELECT MAX(`id`) INTO @lastIdTime FROM `date_dim`;
  SELECT COUNT(`id`) INTO @changeAmount FROM `player_former_world_fact`
  WHERE `idDate` = @idDate AND `idFormerWorld` = idWorld;
  -- pricing table
  -- SELECT `price`
  RETURN @changeAmount * 180;
END;
//
DELIMITER ;

-- procedure inserting daily profit data
DELIMITER //
CREATE PROCEDURE InsertDailyProfitData()
BEGIN
  DECLARE sexChangeProfit FLOAT;
  DECLARE nameChangeProfit FLOAT;
  DECLARE worldChangeProfit FLOAT;
  DECLARE fullProfit FLOAT;
  SELECT MAX(`id`) INTO @maxIdWorld FROM `world_dim`;
  SELECT MAX(`id`) INTO @lastIdTime FROM `date_dim`;
  
  REPEAT
    SET sexChangeProfit = CountDailyProfitFromSex(@maxIdWorld);
    SET nameChangeProfit = CountDailyProfitFromName(@maxIdWorld);
    SET worldChangeProfit = CountDailyProfitFromWorld(@maxIdWorld);
    SET fullProfit = sexChangeProfit + nameChangeProfit + worldChangeProfit;
    
    INSERT INTO `daily_profit` (`idWorld`, `idDate`, `sexChangeProfit`, `nameChangeProfit`, `worldChangeProfit`, `sumOfProfit`)
    VALUES (@maxIdWorld, @lastIdTime, sexChangeProfit, nameChangeProfit, worldChangeProfit, fullProfit);

    SET @maxIdWorld = @maxIdWorld - 1;
  UNTIL @maxIdWorld > 0
  END REPEAT;
END;
//
DELIMITER ;

-- procedure inserting weekly profit data
DELIMITER //
CREATE PROCEDURE InsertWeeklyProfitData()
BEGIN
  DECLARE sexChangeProfit FLOAT;
  DECLARE nameChangeProfit FLOAT;
  DECLARE worldChangeProfit FLOAT;
  DECLARE fullProfit FLOAT;
  SELECT MAX(`id`) INTO @maxIdWorld FROM `world_dim`;
  SELECT MAX(`id`) INTO @lastIdTime FROM `date_dim`;

  REPEAT
    SELECT SUM(`sexChangeProfit`), SUM(`nameChangeProfit`), SUM(`worldChangeProfit`)
    INTO sexChangeProfit, nameChangeProfit, worldChangeProfit
    FROM (
      SELECT `sexChangeProfit`, `nameChangeProfit`, `worldChangeProfit`
      FROM `daily_profit`
      WHERE `idWorld` = @maxIdWorld
      ORDER BY `id` DESC
      LIMIT 1, 7
    ) AS subquery;

    SET fullProfit = sexChangeProfit + nameChangeProfit + worldChangeProfit;

    INSERT INTO `weekly_profit` (`idWorld`, `idDate`, `sexChangeProfit`, `nameChangeProfit`, `worldChangeProfit`, `sumOfProfit`)
    VALUES (@maxIdWorld, @lastIdTime, sexChangeProfit, nameChangeProfit, fullProfit);

    SET @maxIdWorld = @maxIdWorld - 1;
  UNTIL @maxIdWorld > 0
  END REPEAT;
END;
//
DELIMITER ;

-- procedure inserting monthly profit data
DELIMITER //
CREATE PROCEDURE InsertMonthlyProfitData()
BEGIN
  DECLARE sexChangeProfit FLOAT;
  DECLARE nameChangeProfit FLOAT;
  DECLARE worldChangeProfit FLOAT;
  DECLARE fullProfit FLOAT;
  DECLARE daysInMonth INT;
  SELECT MAX(`id`) INTO @maxIdWorld FROM `world_dim`;
  
  SELECT `id`, `fullDate`
  INTO @lastIdTime, @lastDate
  FROM (
    SELECT 'id', 'fullDate'
    FROM `date_dim`
    ORDER BY `id` DESC
    LIMIT 0, 1
  ) AS subquery;

  REPEAT
    SET daysInMonth = DAY(LAST_DAY(@lastDate));
    SELECT SUM(`sexChangeProfit`), SUM(`nameChangeProfit`), SUM(`worldChangeProfit`)
    INTO sexChangeProfit, nameChangeProfit, worldChangeProfit
    FROM (
      SELECT `sexChangeProfit`, `nameChangeProfit`, `worldChangeProfit`
      FROM `daily_profit`
      WHERE `idWorld` = @maxIdWorld
      ORDER BY `id` DESC
      LIMIT 1, daysInMonth
    ) AS subquery;

    SET fullProfit = sexChangeProfit + nameChangeProfit + worldChangeProfit;

    INSERT INTO `monthly_profit` (`idWorld`, `idDate`, `sexChangeProfit`, `nameChangeProfit`, `worldChangeProfit`, `sumOfProfit`)
    VALUES (@maxIdWorld, @lastIdTime, sexChangeProfit, nameChangeProfit, fullProfit);

    SET @maxIdWorld = @maxIdWorld - 1;
  UNTIL @maxIdWorld > 0
  END REPEAT;
END;
//
DELIMITER ;

-- event sheduler for daily profit counting
DELIMITER //
CREATE EVENT DailyProfitEvent
ON SCHEDULE EVERY 1 DAY
STARTS '2020-02-28 00:00:01'
DO BEGIN
  CALL InsertDailyProfitData()
END
//
DELIMITER ;

-- event sheduler for daily profit counting
DELIMITER //
CREATE EVENT WeeklyProfitEvent
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 3 - WEEKDAY(CURRENT_DATE) DAY + INTERVAL 5 MINUTE DO
DO BEGIN
  CALL InsertWeeklyProfitData()
END
//
DELIMITER ;

-- View procedure for daily profit by world and day
-- CREATE PROCEDURE ShowDailyProfit