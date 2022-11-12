CREATE TABLE `comune` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `provincia_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comune_provincia_id_pk` (`provincia_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

