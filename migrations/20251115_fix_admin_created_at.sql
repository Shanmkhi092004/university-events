-- Migration: fix admin_users.created_at zero-dates and enforce default timestamp
-- BACKUP YOUR DATABASE before running this script.

-- 1) Replace zero/empty dates with the current timestamp
UPDATE admin_users
SET created_at = CURRENT_TIMESTAMP
WHERE created_at IN ('0000-00-00', '0000-00-00 00:00:00') OR created_at IS NULL;

-- 2) Ensure the column has a NOT NULL DEFAULT CURRENT_TIMESTAMP (adjust type if needed)
ALTER TABLE admin_users
    MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Verify changes
SELECT id, username, created_at FROM admin_users ORDER BY created_at DESC LIMIT 20;
