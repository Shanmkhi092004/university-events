-- Direct SQL fix for corrupted admin password hashes
-- This script regenerates fresh bcrypt hashes for the admin user
-- Run this in phpMyAdmin or via mysql CLI

USE university_events;

-- Option 1: Reset 'admin' user to bcrypt hash of 'admin123'
-- The hash below is bcrypt hash of 'admin123' (generated fresh)
UPDATE admin_users 
SET password = '$2y$10$YIjlrJ5efxIsIS/VksZv2OPST9/PgBkqquzi.Ee9O2scAvMQGa7t2' 
WHERE username = 'admin';

-- Verify the update
SELECT id, username, password, created_at FROM admin_users WHERE username = 'admin';
