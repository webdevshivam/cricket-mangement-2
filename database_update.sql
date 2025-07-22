
-- Add payment tracking fields to trial_players table
ALTER TABLE `trial_players` 
ADD COLUMN IF NOT EXISTS `payment_status` ENUM('no_payment', 'partial', 'full') DEFAULT 'no_payment' AFTER `cricket_type`,
ADD COLUMN IF NOT EXISTS `verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `payment_status`,
ADD COLUMN IF NOT EXISTS `trial_completed` TINYINT(1) DEFAULT 0 AFTER `verified_at`;

-- Add index for better search performance
ALTER TABLE `trial_players` 
ADD INDEX IF NOT EXISTS `idx_mobile` (`mobile`),
ADD INDEX IF NOT EXISTS `idx_payment_status` (`payment_status`),
ADD INDEX IF NOT EXISTS `idx_trial_city` (`trial_city_id`);

-- Optional: Create a separate payments table for detailed payment tracking
CREATE TABLE IF NOT EXISTS `trial_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trial_player_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','upi','card','online') DEFAULT 'cash',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `payment_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `verified_by` int(11) DEFAULT NULL,
  `status` enum('pending','verified','failed') DEFAULT 'verified',
  PRIMARY KEY (`id`),
  KEY `idx_trial_player` (`trial_player_id`),
  KEY `idx_payment_date` (`payment_date`),
  FOREIGN KEY (`trial_player_id`) REFERENCES `trial_players`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
