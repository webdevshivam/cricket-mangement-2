
-- Complete Database Update Script
-- Run this script to update your local database with all necessary changes

-- ============================================
-- 1. TRIAL MANAGEMENT TABLES
-- ============================================

-- Table for trial managers
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

-- ============================================
-- 2. TRIAL PLAYERS TABLE UPDATES
-- ============================================

-- Add trial manager tracking to trial_players table
ALTER TABLE `trial_players` 
ADD COLUMN IF NOT EXISTS `trial_manager_id` int(11) DEFAULT NULL AFTER `trial_city_id`,
ADD COLUMN IF NOT EXISTS `registered_by_tm` tinyint(1) DEFAULT 0 AFTER `trial_manager_id`;

-- Add payment tracking fields to trial_players table
ALTER TABLE `trial_players` 
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

-- ============================================
-- 3. TRIAL PAYMENTS TABLE (MAIN TABLE)
-- ============================================

-- Create trial_payments table for detailed payment tracking
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

-- ============================================
-- 4. GRADE ASSIGNMENT UPDATES
-- ============================================

-- Update grade_assign table to support trial players
ALTER TABLE `grade_assign` 
ADD COLUMN IF NOT EXISTS `trial_player_id` INT(11) NULL AFTER `player_id`,
ADD INDEX IF NOT EXISTS `idx_trial_player_id` (`trial_player_id`);

-- Make player_id nullable since we'll use either player_id or trial_player_id
ALTER TABLE `grade_assign` 
MODIFY COLUMN `player_id` INT(11) NULL;

-- ============================================
-- 5. FOREIGN KEY CONSTRAINTS
-- ============================================

-- Add foreign key constraints (only if tables exist and columns are compatible)
SET foreign_key_checks = 0;

-- Drop existing constraints if they exist to avoid duplicate key errors
ALTER TABLE `trial_players` DROP FOREIGN KEY IF EXISTS `fk_trial_players_manager`;
ALTER TABLE `trial_payments` DROP FOREIGN KEY IF EXISTS `fk_trial_payments_player`;
ALTER TABLE `trial_payments` DROP FOREIGN KEY IF EXISTS `fk_trial_payments_manager`;
ALTER TABLE `trial_manager_sessions` DROP FOREIGN KEY IF EXISTS `fk_sessions_manager`;

-- Add foreign key constraints
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

SET foreign_key_checks = 1;

-- ============================================
-- 6. PERFORMANCE OPTIMIZATIONS
-- ============================================

-- Add constraint to ensure either player_id or trial_player_id is set in grade_assign
ALTER TABLE `grade_assign` 
ADD CONSTRAINT `chk_player_type` CHECK (
  (player_id IS NOT NULL AND trial_player_id IS NULL) OR 
  (player_id IS NULL AND trial_player_id IS NOT NULL)
);

-- ============================================
-- 7. DATA CLEANUP (OPTIONAL)
-- ============================================

-- Update any existing trial players with default payment status if needed
UPDATE `trial_players` 
SET `payment_status` = 'no_payment' 
WHERE `payment_status` IS NULL;

-- ============================================
-- SCRIPT COMPLETE
-- ============================================

SELECT 'Database update completed successfully!' as status;
