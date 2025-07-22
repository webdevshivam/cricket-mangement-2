
-- League Players Table
CREATE TABLE IF NOT EXISTS `league_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cricketer_type` enum('bowler','batsman','wicket-keeper','all-rounder') NOT NULL,
  `age_group` enum('under_16','above_16') NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `trial_city_id` int(11) DEFAULT NULL,
  `aadhar_document` varchar(255) DEFAULT NULL,
  `marksheet_document` varchar(255) DEFAULT NULL,
  `dob_proof` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `payment_status` enum('no_payment','partial','full') DEFAULT 'no_payment',
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mobile` (`mobile`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_trial_city` (`trial_city_id`),
  KEY `idx_age_group` (`age_group`),
  FOREIGN KEY (`trial_city_id`) REFERENCES `trial_cities`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create uploads directory structure
-- Note: This needs to be created manually in writable/uploads/league_documents/
