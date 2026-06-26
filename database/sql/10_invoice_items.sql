CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_type` VARCHAR(20) NOT NULL,
  `invoice_id` BIGINT UNSIGNED NOT NULL,
  `product_service_id` BIGINT UNSIGNED NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantity` DECIMAL(12,2) NOT NULL DEFAULT 1.00,
  `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_type_invoice_id_index` (`invoice_type`, `invoice_id`),
  KEY `invoice_items_product_service_id_foreign` (`product_service_id`),
  CONSTRAINT `invoice_items_product_service_id_foreign` FOREIGN KEY (`product_service_id`) REFERENCES `product_services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
