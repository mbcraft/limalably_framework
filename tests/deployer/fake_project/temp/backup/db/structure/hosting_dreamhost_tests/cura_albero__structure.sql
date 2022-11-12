CREATE TABLE `cura_albero` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(2048) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

