CREATE TABLE `problema_check_up` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `check_up_albero_id` bigint unsigned DEFAULT NULL,
  `problema_salute_albero_id` bigint unsigned DEFAULT NULL,
  `intervento_cura_albero_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `problema_check_up_check_up_albero_id_pk` (`check_up_albero_id`),
  KEY `problema_check_up_intervento_cura_albero_id_pk` (`intervento_cura_albero_id`),
  KEY `problema_check_up_problema_salute_albero_id_pk` (`problema_salute_albero_id`),
  CONSTRAINT `problema_check_up_check_up_albero_id_pk` FOREIGN KEY (`check_up_albero_id`) REFERENCES `check_up_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `problema_check_up_intervento_cura_albero_id_pk` FOREIGN KEY (`intervento_cura_albero_id`) REFERENCES `intervento_cura_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `problema_check_up_problema_salute_albero_id_pk` FOREIGN KEY (`problema_salute_albero_id`) REFERENCES `problema_salute_albero` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci

