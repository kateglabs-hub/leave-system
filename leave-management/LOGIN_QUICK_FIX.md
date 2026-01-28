# Login Fix - Quick Guide

## The Issue
You can't login with `admin@company.com / admin123`

## The Solution
There are 2 easy ways to fix this:

### ✅ Method 1: Click a Button (Easiest)
1. Go to: http://localhost:8000/public/test.html
2. Scroll down to "Login Issue?" section
3. Click the button: **🔧 Fix Password Hash**
4. Wait for confirmation message
5. Try logging in again!

### ✅ Method 2: Reset Database  
1. In your terminal, from the project directory:
```bash
docker-compose down -v
docker-compose up -d
sleep 15
```

2. Then try logging in again

## After Fixing
- **Login URL:** http://localhost:8000/
- **Email:** admin@company.com
- **Password:** admin123

## What's the Problem?
The database was initialized with a placeholder password hash instead of the correct one for "admin123". The fix script updates it automatically.

## Need More Help?
See [FIX_LOGIN.md](FIX_LOGIN.md) for detailed options.
