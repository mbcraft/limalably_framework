-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql.mbcraftlab.it
-- Creato il: Ago 16, 2022 alle 10:05
-- Versione del server: 8.0.28-0ubuntu0.20.04.3
-- Versione PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mbcraftlab_test`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `albero`
--

CREATE TABLE `albero` (
  `id` bigint UNSIGNED NOT NULL,
  `data_piantumazione` date DEFAULT NULL,
  `latitudine` float NOT NULL,
  `longitudine` float NOT NULL,
  `specie_albero_id` bigint UNSIGNED DEFAULT NULL,
  `comune_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `check_up_albero`
--

CREATE TABLE `check_up_albero` (
  `id` bigint UNSIGNED NOT NULL,
  `albero_id` bigint UNSIGNED DEFAULT NULL,
  `data_check_up` date NOT NULL,
  `esito` tinyint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `comune`
--

CREATE TABLE `comune` (
  `id` bigint UNSIGNED NOT NULL,
  `nome` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `provincia_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `cura_albero`
--

CREATE TABLE `cura_albero` (
  `id` bigint UNSIGNED NOT NULL,
  `descrizione` varchar(2048) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(128) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `intervento_cura_albero`
--

CREATE TABLE `intervento_cura_albero` (
  `id` bigint UNSIGNED NOT NULL,
  `descrizione_aggiuntiva` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  `cura_albero_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `problema_check_up`
--

CREATE TABLE `problema_check_up` (
  `id` bigint NOT NULL,
  `check_up_albero_id` bigint UNSIGNED DEFAULT NULL,
  `problema_salute_albero_id` bigint UNSIGNED DEFAULT NULL,
  `intervento_cura_albero_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `problema_salute_albero`
--

CREATE TABLE `problema_salute_albero` (
  `id` bigint UNSIGNED NOT NULL,
  `nome` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `provincia`
--

CREATE TABLE `provincia` (
  `id` bigint UNSIGNED NOT NULL,
  `nome` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `regione_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `regione`
--

CREATE TABLE `regione` (
  `id` bigint UNSIGNED NOT NULL,
  `nome` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `specie_albero`
--

CREATE TABLE `specie_albero` (
  `id` bigint UNSIGNED NOT NULL,
  `nome` varchar(256) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `albero`
--
ALTER TABLE `albero`
  ADD PRIMARY KEY (`id`),
  ADD KEY `albero_comune_id_pk` (`comune_id`),
  ADD KEY `albero_specie_albero_id_pk` (`specie_albero_id`);

--
-- Indici per le tabelle `check_up_albero`
--
ALTER TABLE `check_up_albero`
  ADD PRIMARY KEY (`id`),
  ADD KEY `check_up_albero_albero_id_pk` (`albero_id`);

--
-- Indici per le tabelle `comune`
--
ALTER TABLE `comune`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comune_provincia_id_pk` (`provincia_id`);

--
-- Indici per le tabelle `cura_albero`
--
ALTER TABLE `cura_albero`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `intervento_cura_albero`
--
ALTER TABLE `intervento_cura_albero`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intervento_cura_albero_cura_albero_id_pk` (`cura_albero_id`);

--
-- Indici per le tabelle `problema_check_up`
--
ALTER TABLE `problema_check_up`
  ADD PRIMARY KEY (`id`),
  ADD KEY `problema_check_up_check_up_albero_id_pk` (`check_up_albero_id`),
  ADD KEY `problema_check_up_intervento_cura_albero_id_pk` (`intervento_cura_albero_id`),
  ADD KEY `problema_check_up_problema_salute_albero_id_pk` (`problema_salute_albero_id`);

--
-- Indici per le tabelle `problema_salute_albero`
--
ALTER TABLE `problema_salute_albero`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `provincia`
--
ALTER TABLE `provincia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provincia_regione_id_pk` (`regione_id`);

--
-- Indici per le tabelle `regione`
--
ALTER TABLE `regione`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `specie_albero`
--
ALTER TABLE `specie_albero`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `albero`
--
ALTER TABLE `albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `check_up_albero`
--
ALTER TABLE `check_up_albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `comune`
--
ALTER TABLE `comune`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `cura_albero`
--
ALTER TABLE `cura_albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `intervento_cura_albero`
--
ALTER TABLE `intervento_cura_albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `problema_check_up`
--
ALTER TABLE `problema_check_up`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `problema_salute_albero`
--
ALTER TABLE `problema_salute_albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `provincia`
--
ALTER TABLE `provincia`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `regione`
--
ALTER TABLE `regione`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `specie_albero`
--
ALTER TABLE `specie_albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `albero`
--
ALTER TABLE `albero`
  ADD CONSTRAINT `albero_comune_id_pk` FOREIGN KEY (`comune_id`) REFERENCES `comune` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `albero_specie_albero_id_pk` FOREIGN KEY (`specie_albero_id`) REFERENCES `specie_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `check_up_albero`
--
ALTER TABLE `check_up_albero`
  ADD CONSTRAINT `check_up_albero_albero_id_pk` FOREIGN KEY (`albero_id`) REFERENCES `albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `comune`
--
ALTER TABLE `comune`
  ADD CONSTRAINT `comune_provincia_id_pk` FOREIGN KEY (`provincia_id`) REFERENCES `provincia` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `intervento_cura_albero`
--
ALTER TABLE `intervento_cura_albero`
  ADD CONSTRAINT `intervento_cura_albero_cura_albero_id_pk` FOREIGN KEY (`cura_albero_id`) REFERENCES `cura_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `problema_check_up`
--
ALTER TABLE `problema_check_up`
  ADD CONSTRAINT `problema_check_up_check_up_albero_id_pk` FOREIGN KEY (`check_up_albero_id`) REFERENCES `check_up_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `problema_check_up_intervento_cura_albero_id_pk` FOREIGN KEY (`intervento_cura_albero_id`) REFERENCES `intervento_cura_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `problema_check_up_problema_salute_albero_id_pk` FOREIGN KEY (`problema_salute_albero_id`) REFERENCES `problema_salute_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `provincia`
--
ALTER TABLE `provincia`
  ADD CONSTRAINT `provincia_regione_id_pk` FOREIGN KEY (`regione_id`) REFERENCES `regione` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
--
-- Struttura della tabella `targhetta_albero`
--

CREATE TABLE `targhetta_albero` (
  `id` bigint UNSIGNED NOT NULL,
  `codice_targhetta` varchar(256) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `targhetta_albero`
--
ALTER TABLE `targhetta_albero`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `targhetta_albero`
--
ALTER TABLE `targhetta_albero`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
  
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
