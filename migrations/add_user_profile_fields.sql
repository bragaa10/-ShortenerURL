-- =====================================================
-- Migração: Adicionar colunas em falta na tabela user
-- Executar no phpMyAdmin ou MySQL Workbench
-- =====================================================

ALTER TABLE `user`
    ADD COLUMN IF NOT EXISTS `password_reset_token` VARCHAR(255) NULL AFTER `auth_key`,
    ADD COLUMN IF NOT EXISTS `profile_bio` TEXT NULL AFTER `role`,
    ADD COLUMN IF NOT EXISTS `profile_company` VARCHAR(150) NULL AFTER `profile_bio`,
    ADD COLUMN IF NOT EXISTS `profile_website` VARCHAR(255) NULL AFTER `profile_company`;

-- Verificar resultado
DESCRIBE `user`;
