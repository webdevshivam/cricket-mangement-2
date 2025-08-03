
-- Fix trial_payments table structure
-- Run this SQL file to resolve the 'updated_at' column error

-- Drop the table if it exists and recreate with proper structure
DROP TABLE IF EXISTS `trial_payments`;

-- Create trial_payments table with all required columns
CREATE TABLE `trial_payments` (
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
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trial_player_id` (`trial_player_id`),
  KEY `trial_manager_id` (`trial_manager_id`),
  KEY `payment_date` (`payment_date`),
  KEY `payment_method` (`payment_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraints
ALTER TABLE `trial_payments` 
ADD CONSTRAINT `fk_trial_payments_player` 
FOREIGN KEY (`trial_player_id`) REFERENCES `trial_players`(`id`) ON DELETE CASCADE;

ALTER TABLE `trial_payments` 
ADD CONSTRAINT `fk_trial_payments_manager` 
FOREIGN KEY (`trial_manager_id`) REFERENCES `trial_managers`(`id`) ON DELETE SET NULL;
