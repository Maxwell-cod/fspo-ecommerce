# 🔧 ADMIN LOGIN TROUBLESHOOTING

## Problem: "Invalid email or password" Error

You're getting an authentication error when trying to login with:
- Email: `admin@fspoltd.rw`
- Password: `admin123`

---

## Root Cause

The admin user in the PostgreSQL database on Render may not have been created, or the password hash is incorrect.

---

## Solution 1: Quick Fix (Recommended)

### Step 1: Get Your Render PostgreSQL Credentials
You need:
- Host: `dpg-d791braa214c73aids1g-a.oregon-postgres.render.com`
- Port: `5432`
- Database: `fspo_db_snv4`
- User: `fspo_db_snv4_user`
- Password: (Your PostgreSQL password from Render dashboard)

### Step 2: Connect to PostgreSQL
```bash
# Using psql command line
psql -h dpg-d791braa214c73aids1g-a.oregon-postgres.render.com \
     -U fspo_db_snv4_user \
     -d fspo_db_snv4 \
     -p 5432
```

### Step 3: Run the Fix Query
Copy and paste this into your PostgreSQL connection:

```sql
UPDATE users 
SET password = '$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG'
WHERE email = 'admin@fspoltd.rw';

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
```

### Step 4: Verify the Fix
```sql
SELECT id, name, email, role FROM users WHERE email = 'admin@fspoltd.rw';
```

You should see:
```
 id | name  |      email       | role
----+-------+------------------+-------
  1 | Admin | admin@fspoltd.rw | admin
```

### Step 5: Try Login Again
- URL: https://fspo-ecommerce.onrender.com/login.php
- Email: `admin@fspoltd.rw`
- Password: `admin123`
- Should now work! ✅

---

## Solution 2: Using Render Dashboard Console

1. Go to https://dashboard.render.com
2. Select your PostgreSQL database service
3. Go to "Console" tab
4. Run the SQL queries from Solution 1, Step 3

---

## Solution 3: Create a New Admin User

If you want to create a different admin account:

```sql
INSERT INTO users (name, email, phone, password, role)
VALUES (
    'Admin 2',
    'admin2@fspoltd.rw',
    '+250 785 723 677',
    '$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG',
    'admin'
);

-- Login with: admin2@fspoltd.rw / admin123
```

---

## Verification Checklist

✅ Check these in order:

### 1. Is the Database Connected?
```sql
SELECT COUNT(*) FROM users;
```
Should return a number (not an error).

### 2. Does the Admin User Exist?
```sql
SELECT * FROM users WHERE email = 'admin@fspoltd.rw';
```
Should return one row.

### 3. Is the Password Hash Correct?
```sql
SELECT password FROM users WHERE email = 'admin@fspoltd.rw';
```
Should show: `$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG`

### 4. Is the Role Set to 'admin'?
```sql
SELECT role FROM users WHERE email = 'admin@fspoltd.rw';
```
Should show: `admin`

---

## Password Hash Reference

For the password `admin123`, the correct bcrypt hash is:
```
$2y$10$NHRpHRYRT6MsNcAowyXNQOEPSHAJv.Yiucr3pwnfIUu4TbBNufLvG
```

This is a bcrypt hash with cost factor 10. If you see a different hash, the password won't match.

---

## If Still Not Working

### Check These Things:

1. **Email Case Sensitivity**
   - Email in database: `admin@fspoltd.rw` (lowercase)
   - Email you're entering: Should also be lowercase
   - PostgreSQL is case-sensitive for emails

2. **Password Spaces**
   - Make sure no extra spaces in password
   - Password should be exactly: `admin123` (no spaces)

3. **Database Connection**
   - Verify PostgreSQL on Render is still running
   - Check Render logs for connection errors

4. **Clear Browser Cache**
   - Clear cookies for the domain
   - Try in incognito/private window
   - Try different browser

5. **Check Application Logs**
   - Go to Render dashboard
   - Check web service logs for PHP errors
   - Look for database connection issues

---

## File References

Files related to authentication:
- `login.php` - Login form and logic
- `includes/config.php` - Database configuration
- `includes/header.php` - Session handling
- `fix-admin-password.sql` - SQL fix file (in repository)
- `generate-admin-hash.php` - Hash generator (in repository)

---

## Still Having Issues?

Try these debugging steps:

### 1. Test Database Connection
Create a test file at: `test-db.php`
```php
<?php
require_once 'includes/config.php';
try {
    $db = getDB();
    $users = $db->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "✅ Database connected! Total users: " . $users['count'];
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>
```

Access: https://fspo-ecommerce.onrender.com/test-db.php

### 2. Check User in Database
Create a test file: `test-admin.php`
```php
<?php
require_once 'includes/config.php';
try {
    $db = getDB();
    $user = $db->prepare("SELECT * FROM users WHERE email = ?")->execute(['admin@fspoltd.rw']);
    $result = $db->prepare("SELECT * FROM users WHERE email = ?")->fetch(PDO::FETCH_ASSOC);
    
    // Can't directly show password, but can verify hash
    $test_pass = "admin123";
    if ($result && password_verify($test_pass, $result['password'])) {
        echo "✅ Admin user exists and password is correct!";
    } else {
        echo "❌ Admin user not found or password mismatch!";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

Access: https://fspo-ecommerce.onrender.com/test-admin.php

---

## Next Steps

1. ✅ Run the SQL fix from Solution 1
2. ✅ Verify the admin user exists
3. ✅ Try logging in again
4. ✅ If it works, delete test files (`test-db.php`, `test-admin.php`)
5. ✅ Change the admin password to something secure

Your admin access should be working soon! 🔑
