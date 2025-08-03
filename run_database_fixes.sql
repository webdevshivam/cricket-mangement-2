
-- Master database fix script
-- Run this file to apply all fixes in the correct order

-- Disable foreign key checks temporarily
SET foreign_key_checks = 0;

-- 1. Create trial_managers table first (required for foreign keys)
SOURCE database_fix_trial_managers.sql;

-- 2. Fix trial_players table 
SOURCE database_fix_trial_players.sql;

-- 3. Fix trial_payments table
SOURCE database_fix_trial_payments.sql;

-- Re-enable foreign key checks
SET foreign_key_checks = 1;

SELECT 'All database fixes applied successfully!' as status;
