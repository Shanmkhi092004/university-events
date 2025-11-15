-- Conservative migration: attempt to ALTER the existing `events` table to ensure
-- `event_id` is an AUTO_INCREMENT primary key. This avoids renaming/recreating the
-- table unless absolutely necessary.
-- BACKUP YOUR DATABASE before running this script.

USE university_events;

-- Build a conditional ALTER statement based on the current schema.
-- The statement chosen will be one of:
--  * ALTER TABLE events ADD COLUMN event_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST
--  * ALTER TABLE events MODIFY event_id INT NOT NULL AUTO_INCREMENT
--  * or a harmless SELECT message if no action is required or the table doesn't exist.

SELECT
    CASE
        WHEN (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'events') = 0
            THEN 'SELECT "Table events does not exist; nothing to do." AS info'
        WHEN (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'events' AND column_name = 'event_id') = 0
            THEN 'ALTER TABLE events ADD COLUMN event_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST'
        WHEN (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'events' AND column_name = 'event_id' AND EXTRA LIKE "%auto_increment%") = 0
            THEN 'ALTER TABLE events MODIFY event_id INT NOT NULL AUTO_INCREMENT'
        ELSE 'SELECT "events.event_id already AUTO_INCREMENT; nothing to do." AS info'
    END
INTO @sql;

-- Execute the chosen statement (may be ALTER or a harmless SELECT message)
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure AUTO_INCREMENT counter is set to max(event_id)+1 to avoid collisions on next insert
-- (this is safe even if event_id was just created)
SET @set_ai_sql = CONCAT('ALTER TABLE events AUTO_INCREMENT = ',
        COALESCE((SELECT MAX(event_id) FROM events), 0) + 1);

PREPARE stmt2 FROM @set_ai_sql;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Verification output: show current schema and a sample of rows
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, COLUMN_KEY, EXTRA
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'events' AND column_name = 'event_id';

SELECT * FROM events ORDER BY created_at DESC LIMIT 10;

-- Note: if this migration fails due to schema constraints (e.g. duplicate keys or
-- existing bad data), review the table and consider the safe fallback migration
-- that creates a new table and copies data. The previous version of this
-- migration (recreate + copy) is available in the repository as an alternative.
