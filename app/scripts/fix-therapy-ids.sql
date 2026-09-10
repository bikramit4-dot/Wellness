-- ============================================================================
-- Fix Therapy IDs for Admin Panel
-- Run this if therapies cannot be edited or deleted in admin panel
-- ============================================================================

USE wellness;

-- Step 1: Check if id column exists
-- If not, add it
ALTER TABLE therapies 
ADD COLUMN IF NOT EXISTS id INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE FIRST;

-- Step 2: Check current state
SELECT 'Current therapies:' AS status;
SELECT id, slug, title FROM therapies ORDER BY slug;

-- Step 3: If IDs are missing/zero, re-index them
-- This updates all therapy IDs to be sequential numbers
SET @new_id = 0;
UPDATE therapies 
SET id = (@new_id := @new_id + 1)
ORDER BY slug;

-- Step 4: Reset auto-increment
ALTER TABLE therapies AUTO_INCREMENT = 1;
SELECT MAX(id) + 1 INTO @next_id FROM therapies;
SET @sql = CONCAT('ALTER TABLE therapies AUTO_INCREMENT = ', @next_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Verify
SELECT 'After fix:' AS status;
SELECT id, slug, title FROM therapies ORDER BY id;

SELECT 'Therapy IDs fixed! Admin edit/delete should now work.' AS result;
