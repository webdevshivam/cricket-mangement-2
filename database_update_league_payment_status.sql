
-- Update league_players table to use correct payment status values
ALTER TABLE `league_players` 
MODIFY COLUMN `payment_status` ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid';

-- Update any existing records that might have old values
UPDATE `league_players` 
SET `payment_status` = 'unpaid' 
WHERE `payment_status` NOT IN ('unpaid', 'paid');
