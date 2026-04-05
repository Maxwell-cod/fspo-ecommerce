-- Fix admin user password for Render PostgreSQL
-- Run this on the Render PostgreSQL database to fix the admin login

-- Update existing admin user with correct password hash
UPDATE users 
SET password = '$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG'
WHERE email = 'admin@fspoltd.rw';

-- If user doesn't exist, insert it
INSERT INTO users (name, email, phone, password, role)
VALUES (
    'Admin',
    'admin@fspoltd.rw',
    '+250 785 723 677',
    '$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG',
    'admin'
) ON CONFLICT (email) DO UPDATE SET 
  password = '$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG',
  role = 'admin';

-- Verify the user was created/updated
SELECT id, name, email, role FROM users WHERE email = 'admin@fspoltd.rw';
