
-- Add payment tracking fields to trial_players table
ALTER TABLE `trial_players` 
ADD COLUMN `payment_status` ENUM('not_verified', 'partial_paid', 'full_paid') DEFAULT 'not_verified' AFTER `cricket_type`,
ADD COLUMN `verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `payment_status`,
ADD COLUMN `trial_completed` TINYINT(1) DEFAULT 0 AFTER `verified_at`;

-- Add index for better search performance
ALTER TABLE `trial_players` 
ADD INDEX `idx_mobile` (`mobile`),
ADD INDEX `idx_payment_status` (`payment_status`);
