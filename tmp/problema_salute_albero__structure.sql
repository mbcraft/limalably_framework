CREATE TABLE `problema_salute_albero` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

