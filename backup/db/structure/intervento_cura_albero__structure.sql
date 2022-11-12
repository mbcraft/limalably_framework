CREATE TABLE `intervento_cura_albero` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descrizione_aggiuntiva` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL,
  `cura_albero_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `intervento_cura_albero_cura_albero_id_pk` (`cura_albero_id`),
  CONSTRAINT `intervento_cura_albero_cura_albero_id_pk` FOREIGN KEY (`cura_albero_id`) REFERENCES `cura_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

