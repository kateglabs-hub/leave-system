# Fix Login Issue - Password Hash Mismatch

## The Problem
The login API returns "Invalid email or password" even though the user exists. This is because the database has an incorrect password hash for the admin user.

## The Solution

You have several options to fix this:

### Option 1: Access the Fix Script (Easiest)
Visit this URL in your browser:
```
http://localhost:8000/api/fix-password.php
```

This script will:
- Check if the admin user exists
- Verify if the password hash is correct
- If incorrect, update it automatically
- Display confirmation message

### Option 2: Reset Database (Nuclear Option)
If the script doesn't work, reset the entire database:

From the project root directory, run:
```bash
docker-compose down -v
docker-compose up -d
sleep 10
```

Then wait 10-15 seconds for MySQL to fully initialize, and the schema.sql will be re-run with the **correct** password hash.

### Option 3: Manual Database Update (Advanced)
Access PHPMyAdmin at http://localhost:8080 and:
1. Login with: root / root_password
2. Select "leave_management" database
3. Click "users" table
4. Click Edit on the admin user row
5. Set password to: `$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm`
6. Click "Go"

### Option 4: Direct MySQL Command
```bash
docker-compose exec mysql mysql -u leave_user -p"leave_password" leave_management -e "UPDATE users SET password = '\$2y\$10\$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm' WHERE email = 'admin@company.com';"
```

## After the Fix
You should be able to login with:
- **Email:** admin@company.com
- **Password:** admin123

Then visit http://localhost:8000/ to see the dashboard.

## What Changed
- Updated `database/schema.sql` to use the correct password hash
- Created `api/fix-password.php` to automatically fix the issue
- Created `reset-db.sh` to reset the database if needed

## Password Hash Details
- Password: `admin123`
- Algorithm: bcrypt
- Cost: 10
- Hash: `$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm`

This hash was verified to correctly decrypt to "admin123" using password_verify() in PHP.
