-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 25, 2026
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestione_eventi`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `soggetto`
--

CREATE TABLE `soggetto` (
  `id_soggetto` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_soggetto` int(10) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `artista`
--

CREATE TABLE `artista` (
  `id_artista` int(10) UNSIGNED NOT NULL,
  `id_soggetto` int(10) UNSIGNED NOT NULL,
  `nome_arte` varchar(100) NOT NULL,
  `genere_musicale` varchar(80) DEFAULT NULL,
  `nazionalita` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sponsor`
--

CREATE TABLE `sponsor` (
  `id_sponsor` int(10) UNSIGNED NOT NULL,
  `id_soggetto` int(10) UNSIGNED NOT NULL,
  `settore_commerciale` varchar(100) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `luogo`
--

CREATE TABLE `luogo` (
  `id_luogo` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `indirizzo` varchar(150) NOT NULL,
  `citta` varchar(80) NOT NULL,
  `capienza` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `evento`
--

CREATE TABLE `evento` (
  `id_evento` int(10) UNSIGNED NOT NULL,
  `id_luogo` int(10) UNSIGNED NOT NULL,
  `titolo` varchar(120) NOT NULL,
  `data_evento` date NOT NULL,
  `ora` time NOT NULL,
  `descrizione` text DEFAULT NULL,
  `genere` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `importo` decimal(10,2) NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `esito` varchar(30) NOT NULL,
  `data_pagamento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `biglietto`
--

CREATE TABLE `biglietto` (
  `id_biglietto` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_pagamento` int(10) UNSIGNED NOT NULL,
  `id_evento` int(10) UNSIGNED NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `posto` varchar(20) DEFAULT NULL,
  `stato` varchar(30) NOT NULL DEFAULT 'valido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `evento_artista`
--

CREATE TABLE `evento_artista` (
  `id_evento` int(10) UNSIGNED NOT NULL,
  `id_artista` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `evento_sponsor`
--

CREATE TABLE `evento_sponsor` (
  `id_evento` int(10) UNSIGNED NOT NULL,
  `id_sponsor` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `soggetto`
--
ALTER TABLE `soggetto`
  ADD PRIMARY KEY (`id_soggetto`);

--
-- Indici per le tabelle `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uk_cliente_soggetto` (`id_soggetto`);

--
-- Indici per le tabelle `artista`
--
ALTER TABLE `artista`
  ADD PRIMARY KEY (`id_artista`),
  ADD UNIQUE KEY `uk_artista_soggetto` (`id_soggetto`);

--
-- Indici per le tabelle `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`id_sponsor`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uk_sponsor_soggetto` (`id_soggetto`);

--
-- Indici per le tabelle `luogo`
--
ALTER TABLE `luogo`
  ADD PRIMARY KEY (`id_luogo`);

--
-- Indici per le tabelle `evento`
--
ALTER TABLE `evento`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `fk_evento_luogo` (`id_luogo`),
  ADD KEY `idx_evento_data` (`data_evento`);

--
-- Indici per le tabelle `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `idx_pagamento_cliente` (`id_cliente`);

--
-- Indici per le tabelle `biglietto`
--
ALTER TABLE `biglietto`
  ADD PRIMARY KEY (`id_biglietto`),
  ADD KEY `idx_biglietto_cliente` (`id_cliente`),
  ADD KEY `fk_biglietto_pagamento` (`id_pagamento`),
  ADD KEY `idx_biglietto_evento` (`id_evento`);

--
-- Indici per le tabelle `evento_artista`
--
ALTER TABLE `evento_artista`
  ADD PRIMARY KEY (`id_evento`,`id_artista`),
  ADD KEY `fk_evento_artista_artista` (`id_artista`);

--
-- Indici per le tabelle `evento_sponsor`
--
ALTER TABLE `evento_sponsor`
  ADD PRIMARY KEY (`id_evento`,`id_sponsor`),
  ADD KEY `fk_evento_sponsor_sponsor` (`id_sponsor`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `soggetto`
--
ALTER TABLE `soggetto`
  MODIFY `id_soggetto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `artista`
--
ALTER TABLE `artista`
  MODIFY `id_artista` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `sponsor`
--
ALTER TABLE `sponsor`
  MODIFY `id_sponsor` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `luogo`
--
ALTER TABLE `luogo`
  MODIFY `id_luogo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `evento`
--
ALTER TABLE `evento`
  MODIFY `id_evento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `biglietto`
--
ALTER TABLE `biglietto`
  MODIFY `id_biglietto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_soggetto` FOREIGN KEY (`id_soggetto`) REFERENCES `soggetto` (`id_soggetto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `artista`
--
ALTER TABLE `artista`
  ADD CONSTRAINT `fk_artista_soggetto` FOREIGN KEY (`id_soggetto`) REFERENCES `soggetto` (`id_soggetto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `sponsor`
--
ALTER TABLE `sponsor`
  ADD CONSTRAINT `fk_sponsor_soggetto` FOREIGN KEY (`id_soggetto`) REFERENCES `soggetto` (`id_soggetto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `fk_evento_luogo` FOREIGN KEY (`id_luogo`) REFERENCES `luogo` (`id_luogo`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `fk_pagamento_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `biglietto`
--
ALTER TABLE `biglietto`
  ADD CONSTRAINT `fk_biglietto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_biglietto_evento` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_biglietto_pagamento` FOREIGN KEY (`id_pagamento`) REFERENCES `pagamento` (`id_pagamento`) ON UPDATE CASCADE;

--
-- Limiti per la tabella `evento_artista`
--
ALTER TABLE `evento_artista`
  ADD CONSTRAINT `fk_evento_artista_artista` FOREIGN KEY (`id_artista`) REFERENCES `artista` (`id_artista`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evento_artista_evento` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `evento_sponsor`
--
ALTER TABLE `evento_sponsor`
  ADD CONSTRAINT `fk_evento_sponsor_evento` FOREIGN KEY (`id_evento`) REFERENCES `evento` (`id_evento`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evento_sponsor_sponsor` FOREIGN KEY (`id_sponsor`) REFERENCES `sponsor` (`id_sponsor`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
