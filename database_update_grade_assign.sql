
-- Update grade_assign table to support trial players
ALTER TABLE `grade_assign` 
ADD COLUMN IF NOT EXISTS `trial_player_id` INT(11) NULL AFTER `player_id`,
ADD INDEX IF NOT EXISTS `idx_trial_player_id` (`trial_player_id`);

-- Make player_id nullable since we'll use either player_id or trial_player_id
ALTER TABLE `grade_assign` 
MODIFY COLUMN `player_id` INT(11) NULL;

-- Add constraint to ensure either player_id or trial_player_id is set, but not both
ALTER TABLE `grade_assign` 
ADD CONSTRAINT `chk_player_type` CHECK (
  (player_id IS NOT NULL AND trial_player_id IS NULL) OR 
  (player_id IS NULL AND trial_player_id IS NOT NULL)
);
