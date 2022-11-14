CREATE TABLE `check_up_albero` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `albero_id` bigint unsigned DEFAULT NULL,
  `data` date NOT NULL,
  `esito` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `check_up_albero_albero_id_pk` (`albero_id`),
  CONSTRAINT `check_up_albero_albero_id_pk` FOREIGN KEY (`albero_id`) REFERENCES `albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

