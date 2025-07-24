
-- Create OTP settings table
CREATE TABLE IF NOT EXISTS `otp_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `trial_otp_enabled` tinyint(1) NOT NULL DEFAULT 0,
    `league_otp_enabled` tinyint(1) NOT NULL DEFAULT 0,
    `otp_expiry_minutes` int(11) NOT NULL DEFAULT 10,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create OTP verifications table
CREATE TABLE IF NOT EXISTS `otp_verifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `otp_code` varchar(6) NOT NULL,
    `registration_type` enum('trial','league') NOT NULL,
    `registration_data` longtext NOT NULL,
    `is_verified` tinyint(1) NOT NULL DEFAULT 0,
    `expires_at` timestamp NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `email_type_idx` (`email`, `registration_type`),
    KEY `expires_at_idx` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default OTP settings
INSERT INTO `otp_settings` (`trial_otp_enabled`, `league_otp_enabled`, `otp_expiry_minutes`) 
VALUES (0, 0, 10) 
ON DUPLICATE KEY UPDATE 
    `trial_otp_enabled` = VALUES(`trial_otp_enabled`),
    `league_otp_enabled` = VALUES(`league_otp_enabled`),
    `otp_expiry_minutes` = VALUES(`otp_expiry_minutes`);
