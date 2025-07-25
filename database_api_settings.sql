
-- Create API Settings table
CREATE TABLE IF NOT EXISTS `api_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openweather_enabled` tinyint(1) DEFAULT 0,
  `openweather_api_key` varchar(255) DEFAULT '',
  `openweather_api_url` varchar(500) DEFAULT 'https://api.openweathermap.org/data/2.5',
  `razorpay_enabled` tinyint(1) DEFAULT 0,
  `razorpay_key_id` varchar(255) DEFAULT '',
  `razorpay_key_secret` varchar(255) DEFAULT '',
  `sms_enabled` tinyint(1) DEFAULT 0,
  `sms_api_key` varchar(255) DEFAULT '',
  `sms_api_secret` varchar(255) DEFAULT '',
  `sms_api_url` varchar(500) DEFAULT '',
  `email_enabled` tinyint(1) DEFAULT 1,
  `email_host` varchar(255) DEFAULT 'smtp.gmail.com',
  `email_port` int(11) DEFAULT 587,
  `email_username` varchar(255) DEFAULT '',
  `email_password` varchar(255) DEFAULT '',
  `email_encryption` varchar(10) DEFAULT 'tls',
  `email_from_address` varchar(255) DEFAULT '',
  `email_from_name` varchar(255) DEFAULT 'Cricket League',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default API settings
INSERT INTO `api_settings` (
  `openweather_enabled`, `openweather_api_key`, `openweather_api_url`,
  `razorpay_enabled`, `razorpay_key_id`, `razorpay_key_secret`,
  `sms_enabled`, `sms_api_key`, `sms_api_secret`, `sms_api_url`,
  `email_enabled`, `email_host`, `email_port`, `email_username`, 
  `email_password`, `email_encryption`, `email_from_address`, `email_from_name`
) VALUES (
  0, '', 'https://api.openweathermap.org/data/2.5',
  0, '', '',
  0, '', '', '',
  1, 'smtp.gmail.com', 587, '', 
  '', 'tls', '', 'Cricket League'
) ON DUPLICATE KEY UPDATE 
    `openweather_enabled` = VALUES(`openweather_enabled`),
    `openweather_api_key` = VALUES(`openweather_api_key`),
    `openweather_api_url` = VALUES(`openweather_api_url`),
    `razorpay_enabled` = VALUES(`razorpay_enabled`),
    `razorpay_key_id` = VALUES(`razorpay_key_id`),
    `razorpay_key_secret` = VALUES(`razorpay_key_secret`),
    `sms_enabled` = VALUES(`sms_enabled`),
    `sms_api_key` = VALUES(`sms_api_key`),
    `sms_api_secret` = VALUES(`sms_api_secret`),
    `sms_api_url` = VALUES(`sms_api_url`),
    `email_enabled` = VALUES(`email_enabled`),
    `email_host` = VALUES(`email_host`),
    `email_port` = VALUES(`email_port`),
    `email_username` = VALUES(`email_username`),
    `email_password` = VALUES(`email_password`),
    `email_encryption` = VALUES(`email_encryption`),
    `email_from_address` = VALUES(`email_from_address`),
    `email_from_name` = VALUES(`email_from_name`);
