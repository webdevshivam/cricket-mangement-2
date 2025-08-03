
-- Add status column to league_players table
ALTER TABLE `league_players` 
ADD COLUMN `status` ENUM('not_selected', 'selected') DEFAULT 'not_selected' AFTER `payment_status`;

-- Update existing records to default status
UPDATE `league_players` 
SET `status` = 'not_selected' 
WHERE `status` IS NULL;
