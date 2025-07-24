
-- Create Tournaments table
CREATE TABLE IF NOT EXISTS `tournaments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `type` enum('knockout','round_robin') NOT NULL DEFAULT 'knockout',
    `status` enum('draft','active','completed','cancelled') NOT NULL DEFAULT 'draft',
    `current_round` int(11) NOT NULL DEFAULT 1,
    `winner_team_id` int(11) DEFAULT NULL,
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_type` (`type`),
    KEY `fk_winner_team` (`winner_team_id`),
    FOREIGN KEY (`winner_team_id`) REFERENCES `teams`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Tournament Matches table
CREATE TABLE IF NOT EXISTS `tournament_matches` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tournament_id` int(11) NOT NULL,
    `round_number` int(11) NOT NULL,
    `match_number` int(11) NOT NULL,
    `team1_id` int(11) NOT NULL,
    `team2_id` int(11) NOT NULL,
    `winner_team_id` int(11) DEFAULT NULL,
    `team1_score` int(11) DEFAULT NULL,
    `team2_score` int(11) DEFAULT NULL,
    `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    `match_date` datetime DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `tournament_id` (`tournament_id`),
    KEY `team1_id` (`team1_id`),
    KEY `team2_id` (`team2_id`),
    KEY `winner_team_id` (`winner_team_id`),
    KEY `idx_round_match` (`round_number`, `match_number`),
    KEY `idx_status` (`status`),
    FOREIGN KEY (`tournament_id`) REFERENCES `tournaments`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`team1_id`) REFERENCES `teams`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`team2_id`) REFERENCES `teams`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`winner_team_id`) REFERENCES `teams`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
