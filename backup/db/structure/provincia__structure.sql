CREATE TABLE `provincia` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `codice` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `regione_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `provincia_regione_id_pk` (`regione_id`),
  CONSTRAINT `provincia_regione_id_pk` FOREIGN KEY (`regione_id`) REFERENCES `regione` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

