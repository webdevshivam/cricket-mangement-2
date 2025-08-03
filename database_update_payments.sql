
-- Add payments table for detailed payment tracking
CREATE TABLE IF NOT EXISTS `trial_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trial_player_id` int(11) NOT NULL,
  `trial_manager_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('offline','online') NOT NULL DEFAULT 'offline',
  `transaction_ref` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `collected_on_trial_day` tinyint(1) DEFAULT 0,
  `collected_by` varchar(100) DEFAULT NULL,
  `payment_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trial_player_id` (`trial_player_id`),
  KEY `trial_manager_id` (`trial_manager_id`),
  KEY `payment_date` (`payment_date`),
  KEY `payment_method` (`payment_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for better performance on date-based queries
ALTER TABLE `trial_players` ADD INDEX `idx_verified_at` (`verified_at`);
ALTER TABLE `trial_players` ADD INDEX `idx_mobile` (`mobile`);

-- Add trial_completed column if not exists
ALTER TABLE `trial_players` ADD COLUMN `trial_completed` tinyint(1) DEFAULT 0;
