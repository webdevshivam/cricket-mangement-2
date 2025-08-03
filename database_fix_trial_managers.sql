
-- Create trial_managers table if it doesn't exist
-- Run this SQL file to ensure trial managers functionality works

CREATE TABLE IF NOT EXISTS `trial_managers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `trial_name` varchar(150) NOT NULL,
  `trial_city_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trial_city_id` (`trial_city_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create trial_manager_sessions table for tracking TM sessions
CREATE TABLE IF NOT EXISTS `trial_manager_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trial_manager_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `trial_manager_id` (`trial_manager_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraint for sessions
ALTER TABLE `trial_manager_sessions` 
ADD CONSTRAINT `fk_sessions_manager` 
FOREIGN KEY (`trial_manager_id`) REFERENCES `trial_managers`(`id`) ON DELETE CASCADE;
