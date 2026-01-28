# 🚀 One-Minute Setup Guide

## Current Status: ✅ Ready to Use!

### Step 1: Fix the Login (30 seconds)
Go to your browser and visit:
```
http://localhost:8000/public/test.html
```

Scroll down and click the button: **🔧 Fix Password Hash**

Wait for the success message ✅

### Step 2: Login to the App (30 seconds)
Visit: `http://localhost:8000/`

Use these credentials:
- **Email:** admin@company.com
- **Password:** admin123

Click "Login" ✅

### Done! 🎉

You now have access to the Leave Management System dashboard!

---

## What If It Doesn't Work?

### Option A: Browser Refresh
Press `Ctrl+F5` (or `Cmd+Shift+R` on Mac) to do a hard refresh

### Option B: Clear Browser Cache
1. Open DevTools (F12)
2. Right-click the refresh button
3. Select "Empty cache and hard refresh"

### Option C: Reset Database
Open terminal and run:
```bash
cd leave-management
docker-compose down -v
docker-compose up -d
sleep 15
```

Then try step 1-2 again.

---

## URLs You'll Need

| Purpose | URL | Notes |
|---------|-----|-------|
| **Main App** | http://localhost:8000/ | Login page |
| **Test Page** | http://localhost:8000/public/test.html | Diagnostics & fixes |
| **Database UI** | http://localhost:8080 | PHPMyAdmin (root/root_password) |
| **API Test** | http://localhost:8000/api/diagnostics.php | System info |

---

## Login Credentials

| Purpose | Email | Password | Role |
|---------|-------|----------|------|
| **Admin** | admin@company.com | admin123 | Full access |
| **HR** | hr@company.com | admin123 | HR functions |
| **Database** | root | root_password | PhpMyAdmin |

---

## Commands You Might Need

```bash
# See if containers are running
docker-compose ps

# View logs
docker-compose logs -f

# Stop everything
docker-compose down

# Start everything
docker-compose up -d

# Start fresh (delete database)
docker-compose down -v && docker-compose up -d
```

---

## Dashboard Features

Once logged in, you can:
- 👤 View your profile
- 🏖️ Request leave (Annual, Sick, etc.)
- 📊 Check leave balance
- 📋 View request history
- ✅ Approve/Reject (if HR/Admin)
- 📈 View reports (if Admin)

---

## Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| **Login fails** | Click "🔧 Fix Password Hash" on test page |
| **Page doesn't load** | Hard refresh: Ctrl+F5 |
| **API not responding** | Restart containers: `docker-compose restart` |
| **Database error** | Reset: `docker-compose down -v && docker-compose up -d` |
| **Can't see dashboard** | Clear browser cache or try incognito window |

---

## Next Steps

1. ✅ Fix password hash (if needed)
2. ✅ Login with admin account
3. ✅ Explore the dashboard
4. ✅ Create a leave request
5. ✅ Check your leave balance
6. ✅ Create another user (as admin) to test workflows

---

## Still Stuck?

1. Check browser console (F12 → Console tab) for errors
2. Visit http://localhost:8000/public/test.html and run tests
3. Check PHPMyAdmin (http://localhost:8080) to verify database
4. Read [GETTING_STARTED.md](GETTING_STARTED.md) for detailed help
5. Read [FIX_LOGIN.md](FIX_LOGIN.md) for all fix options

---

**You're all set! Start with step 1 above! 🚀**
