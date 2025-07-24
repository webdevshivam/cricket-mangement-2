
-- Create Teams table
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

-- Create Team Players table (for player assignments to teams)
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

-- Insert 16 default teams
INSERT INTO `teams` (`name`, `status`) VALUES
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
('Team 16', 'draft')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
