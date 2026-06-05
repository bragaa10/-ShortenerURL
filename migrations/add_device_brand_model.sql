-- Adicionar campos de Marca e Modelo na tabela scan_log
ALTER TABLE `scan_log` 
ADD COLUMN `brand` VARCHAR(100) NULL DEFAULT NULL AFTER `device_type`,
ADD COLUMN `model` VARCHAR(100) NULL DEFAULT NULL AFTER `brand`;
