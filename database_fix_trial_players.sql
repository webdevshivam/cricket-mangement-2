
-- Fix trial_players table structure
-- Run this SQL file to ensure all required columns exist

-- Add missing columns if they don't exist
ALTER TABLE `trial_players` 
ADD COLUMN IF NOT EXISTS `trial_manager_id` int(11) DEFAULT NULL AFTER `trial_city_id`,
ADD COLUMN IF NOT EXISTS `registered_by_tm` tinyint(1) DEFAULT 0 AFTER `trial_manager_id`,
ADD COLUMN IF NOT EXISTS `payment_status` ENUM('no_payment', 'partial', 'full') DEFAULT 'no_payment' AFTER `cricket_type`,
ADD COLUMN IF NOT EXISTS `verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `payment_status`,
ADD COLUMN IF NOT EXISTS `trial_completed` TINYINT(1) DEFAULT 0 AFTER `verified_at`;

-- Add indexes for better performance
ALTER TABLE `trial_players` 
ADD INDEX IF NOT EXISTS `idx_trial_manager` (`trial_manager_id`),
ADD INDEX IF NOT EXISTS `idx_mobile` (`mobile`),
ADD INDEX IF NOT EXISTS `idx_payment_status` (`payment_status`),
ADD INDEX IF NOT EXISTS `idx_trial_city` (`trial_city_id`),
ADD INDEX IF NOT EXISTS `idx_verified_at` (`verified_at`);

-- Add foreign key constraint
ALTER TABLE `trial_players` 
ADD CONSTRAINT `fk_trial_players_manager` 
FOREIGN KEY (`trial_manager_id`) REFERENCES `trial_managers`(`id`) ON DELETE SET NULL;
