
-- Safe Database Migration Script
-- This script preserves all existing data while adding new features
-- Run this script to update your database structure safely

-- Disable foreign key checks temporarily for safe migration
SET foreign_key_checks = 0;

-- ============================================
-- 1. CREATE NEW TABLES (IF NOT EXISTS)
-- ============================================

-- Create trial_managers table if it doesn't exist
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

-- Create trial_payments table if it doesn't exist
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
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trial_player_id` (`trial_player_id`),
  KEY `trial_manager_id` (`trial_manager_id`),
  KEY `payment_date` (`payment_date`),
  KEY `payment_method` (`payment_method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create teams table if it doesn't exist
CREATE TABLE IF NOT EXISTS `teams` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `captain_id` int(11) DEFAULT NULL,
    `coach_name` varchar(100) DEFAULT NULL,
    `status` enum('draft','active','inactive') NOT NULL DEFAULT 'draft',
    `logo` varchar(255) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_name` (`name`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create team_players table if it doesn't exist
CREATE TABLE IF NOT EXISTS `team_players` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `team_id` int(11) NOT NULL,
    `player_id` int(11) NOT NULL,
    `player_type` enum('league','trial') NOT NULL,
    `position` varchar(50) DEFAULT NULL,
    `jersey_number` int(11) DEFAULT NULL,
    `is_captain` tinyint(1) NOT NULL DEFAULT 0,
    `is_vice_captain` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `team_id` (`team_id`),
    KEY `player_type_id` (`player_id`, `player_type`),
    UNIQUE KEY `unique_player_assignment` (`player_id`, `player_type`),
    UNIQUE KEY `unique_jersey_per_team` (`team_id`, `jersey_number`),
    FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 2. ADD NEW COLUMNS TO EXISTING TABLES (SAFE)
-- ============================================

-- Add columns to trial_players table if they don't exist
ALTER TABLE `trial_players` 
ADD COLUMN IF NOT EXISTS `trial_manager_id` int(11) DEFAULT NULL AFTER `trial_city_id`,
ADD COLUMN IF NOT EXISTS `registered_by_tm` tinyint(1) DEFAULT 0 AFTER `trial_manager_id`,
ADD COLUMN IF NOT EXISTS `payment_status` ENUM('no_payment', 'partial', 'full') DEFAULT 'no_payment' AFTER `cricket_type`,
ADD COLUMN IF NOT EXISTS `verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `payment_status`,
ADD COLUMN IF NOT EXISTS `trial_completed` TINYINT(1) DEFAULT 0 AFTER `verified_at`;

-- Add columns to grade_assign table if they don't exist
ALTER TABLE `grade_assign` 
ADD COLUMN IF NOT EXISTS `trial_player_id` INT(11) NULL AFTER `player_id`;

-- Make player_id nullable in grade_assign (preserve existing data)
ALTER TABLE `grade_assign` 
MODIFY COLUMN `player_id` INT(11) NULL;

-- Update league_players payment status enum (preserve existing data)
ALTER TABLE `league_players` 
MODIFY COLUMN `payment_status` ENUM('unpaid', 'paid', 'no_payment', 'partial', 'full') NOT NULL DEFAULT 'unpaid';

-- ============================================
-- 3. ADD INDEXES FOR PERFORMANCE (SAFE)
-- ============================================

-- Add indexes to trial_players table if they don't exist
ALTER TABLE `trial_players` 
ADD INDEX IF NOT EXISTS `idx_trial_manager` (`trial_manager_id`),
ADD INDEX IF NOT EXISTS `idx_mobile` (`mobile`),
ADD INDEX IF NOT EXISTS `idx_payment_status` (`payment_status`),
ADD INDEX IF NOT EXISTS `idx_trial_city` (`trial_city_id`),
ADD INDEX IF NOT EXISTS `idx_verified_at` (`verified_at`);

-- Add index to grade_assign table if it doesn't exist
ALTER TABLE `grade_assign` 
ADD INDEX IF NOT EXISTS `idx_trial_player_id` (`trial_player_id`);

-- ============================================
-- 4. INSERT DEFAULT TEAMS (SAFE)
-- ============================================

-- Insert 16 default teams only if they don't exist
INSERT IGNORE INTO `teams` (`name`, `status`) VALUES
('Team 1', 'draft'),
('Team 2', 'draft'),
('Team 3', 'draft'),
('Team 4', 'draft'),
('Team 5', 'draft'),
('Team 6', 'draft'),
('Team 7', 'draft'),
('Team 8', 'draft'),
('Team 9', 'draft'),
('Team 10', 'draft'),
('Team 11', 'draft'),
('Team 12', 'draft'),
('Team 13', 'draft'),
('Team 14', 'draft'),
('Team 15', 'draft'),
('Team 16', 'draft');

-- ============================================
-- 5. ADD FOREIGN KEY CONSTRAINTS (SAFE)
-- ============================================

-- Drop existing constraints if they exist to avoid errors
ALTER TABLE `trial_players` DROP FOREIGN KEY IF EXISTS `fk_trial_players_manager`;
ALTER TABLE `trial_payments` DROP FOREIGN KEY IF EXISTS `fk_trial_payments_player`;
ALTER TABLE `trial_payments` DROP FOREIGN KEY IF EXISTS `fk_trial_payments_manager`;
ALTER TABLE `trial_manager_sessions` DROP FOREIGN KEY IF EXISTS `fk_sessions_manager`;

-- Add foreign key constraints safely
ALTER TABLE `trial_players` 
ADD CONSTRAINT `fk_trial_players_manager` 
FOREIGN KEY (`trial_manager_id`) REFERENCES `trial_managers`(`id`) ON DELETE SET NULL;

ALTER TABLE `trial_payments` 
ADD CONSTRAINT `fk_trial_payments_player` 
FOREIGN KEY (`trial_player_id`) REFERENCES `trial_players`(`id`) ON DELETE CASCADE;

ALTER TABLE `trial_payments` 
ADD CONSTRAINT `fk_trial_payments_manager` 
FOREIGN KEY (`trial_manager_id`) REFERENCES `trial_managers`(`id`) ON DELETE SET NULL;

ALTER TABLE `trial_manager_sessions` 
ADD CONSTRAINT `fk_sessions_manager` 
FOREIGN KEY (`trial_manager_id`) REFERENCES `trial_managers`(`id`) ON DELETE CASCADE;

-- ============================================
-- 6. UPDATE EXISTING DATA SAFELY
-- ============================================

-- Update any existing trial players with default payment status if NULL
UPDATE `trial_players` 
SET `payment_status` = 'no_payment' 
WHERE `payment_status` IS NULL;

-- Update any existing league players with standardized payment status
UPDATE `league_players` 
SET `payment_status` = 'unpaid' 
WHERE `payment_status` NOT IN ('unpaid', 'paid', 'no_payment', 'partial', 'full');

-- ============================================
-- 7. ADD CONSTRAINTS (SAFE)
-- ============================================

-- Add constraint to ensure either player_id or trial_player_id is set in grade_assign
-- Drop existing constraint if it exists
ALTER TABLE `grade_assign` DROP CONSTRAINT IF EXISTS `chk_player_type`;

-- Add the constraint
ALTER TABLE `grade_assign` 
ADD CONSTRAINT `chk_player_type` CHECK (
  (player_id IS NOT NULL AND trial_player_id IS NULL) OR 
  (player_id IS NULL AND trial_player_id IS NOT NULL)
);

-- Re-enable foreign key checks
SET foreign_key_checks = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Show summary of changes
SELECT 'Safe database migration completed successfully!' as status;

-- Show table counts to verify data preservation
SELECT 
    'trial_players' as table_name, 
    COUNT(*) as record_count 
FROM trial_players
UNION ALL
SELECT 
    'league_players' as table_name, 
    COUNT(*) as record_count 
FROM league_players
UNION ALL
SELECT 
    'teams' as table_name, 
    COUNT(*) as record_count 
FROM teams
UNION ALL
SELECT 
    'trial_managers' as table_name, 
    COUNT(*) as record_count 
FROM trial_managers;
