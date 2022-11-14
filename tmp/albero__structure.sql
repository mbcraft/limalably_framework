CREATE TABLE `albero` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_piantumazione` date DEFAULT NULL,
  `latitudine` float NOT NULL,
  `longitudine` float NOT NULL,
  `specie_albero_id` bigint unsigned DEFAULT NULL,
  `comune_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `albero_comune_id_pk` (`comune_id`),
  KEY `albero_specie_albero_id_pk` (`specie_albero_id`),
  CONSTRAINT `albero_comune_id_pk` FOREIGN KEY (`comune_id`) REFERENCES `comune` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `albero_specie_albero_id_pk` FOREIGN KEY (`specie_albero_id`) REFERENCES `specie_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1086 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

