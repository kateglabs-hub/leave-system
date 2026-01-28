# 🖥️ Frontend Not Working - Troubleshooting Guide

## Quick Diagnosis (Try These First)

### 1. Test Page Availability
Open your browser and go to:
```
http://localhost:8000/public/test.html
```

This diagnostic page will tell you:
- ✅ If the HTML frontend is loading
- ✅ If JavaScript is executing
- ✅ If the backend API is responding
- ✅ If the database is connected

### 2. Check Browser Console
Press **F12** or **Ctrl+Shift+J** (Cmd+Option+J on Mac) to open Developer Tools.

Look for errors like:
- `CORS error` - API not accessible
- `404 not found` - Files not found
- `Failed to fetch` - Backend not responding
- `Uncaught error` - JavaScript error

### 3. Access Main App Directly
Try opening:
```
http://localhost:8000/
```

Expected behavior:
- Should show a **login page** with dark theme
- Should have "Leave Management System" title
- Should have email/password input fields
- Should have a login button

## Common Issues & Fixes

### Issue 1: Blank Page (Nothing Loads)

**Cause**: Frontend files not being served  
**Solution**:

```bash
# Check if app container is running
docker-compose ps

# If app is "Restarting" or "Exit", check logs
docker-compose logs app

# Restart the app container
docker-compose restart app

# Wait 10 seconds and check status
sleep 10
docker-compose ps
```

### Issue 2: "Cannot GET /"

**Cause**: Apache routing not working  
**Solution**:

```bash
# Verify .htaccess is being used
docker-compose exec app a2enmod rewrite

# Restart Apache
docker-compose restart app
```

### Issue 3: 404 Not Found

**Cause**: Files not found or incorrect paths  
**Solution**:

```bash
# Check if public/index.html exists
docker-compose exec app ls -la /var/www/html/public/

# Check if .htaccess exists
docker-compose exec app ls -la /var/www/html/
```

### Issue 4: API Errors in Console

**Cause**: Backend API not responding  
**Solution**:

```bash
# Test if API is responding
curl http://localhost:8000/api/current-user

# Check API logs
docker-compose logs app | grep -i api

# Check if database is connected
docker-compose exec mysql mysql -u leave_user -p leave_management -e "SELECT 1;"
```

### Issue 5: "Failed to fetch" Errors

**Cause**: Backend not responding or CORS issue  
**Solution**:

```bash
# Check if backend is running
docker-compose ps
# All containers should show "Up"

# Test direct backend access
curl http://localhost:8000/api/diagnostics.php

# Check server logs
docker-compose logs app
docker-compose logs mysql
```

### Issue 6: Console Shows PHP Errors

**Cause**: PHP code has errors  
**Solution**:

```bash
# View PHP error logs
docker-compose logs app 2>&1 | grep -i error

# View detailed error log
docker-compose exec app tail -100 /var/log/apache2/php-error.log
```

## Step-by-Step Troubleshooting

### Step 1: Verify Containers Are Running
```bash
docker-compose ps
```

Expected output:
```
NAME                      IMAGE              STATUS
leave_management_app      leave-management   Up 2 minutes
leave_management_mysql    mysql:8.0          Up 2 minutes  
leave_management_phpmyadmin phpmyadmin        Up 2 minutes
```

**If not "Up"**: Run the fix script
```bash
./fix-docker.sh  # or fix-docker.bat on Windows
```

### Step 2: Test Backend Connectivity
```bash
# Test if Apache is responding
curl -i http://localhost:8000/

# Expected: 200 OK with HTML content

# Test if API endpoint exists
curl -i http://localhost:8000/api/current-user

# Expected: 401 Unauthorized (not logged in) with JSON
```

### Step 3: Check Docker Logs
```bash
# All logs
docker-compose logs

# App logs only
docker-compose logs app -f

# MySQL logs only
docker-compose logs mysql

# Last 50 lines
docker-compose logs --tail=50
```

### Step 4: Test Frontend Loading
```bash
# Download and check if HTML file exists
docker-compose exec app curl http://localhost/public/index.html

# Should return HTML content

# Or use test page
docker-compose exec app curl http://localhost/public/test.html
```

### Step 5: Access Test Diagnostics Page
Open in browser:
```
http://localhost:8000/public/test.html
```

Click buttons to run tests and see detailed results.

## Full Reset (Nuclear Option)

If nothing is working, start completely fresh:

```bash
# Stop everything and remove all data
docker-compose down -v

# Remove all Docker images and containers
docker system prune -a

# Rebuild everything
docker-compose up -d --build

# Wait 30 seconds
sleep 30

# Check status
docker-compose ps

# View logs
docker-compose logs
```

## Expected Behavior When Working

### On http://localhost:8000/
1. **Page Loads**: Should see login page within 2 seconds
2. **Styling**: Dark theme with blue accents
3. **Title**: "Leave Management System" in browser tab
4. **Inputs**: Email and password fields visible
5. **No Errors**: Browser console should be clear

### Try Login
Use default credentials:
- Email: `admin@company.com`
- Password: `admin123`

Expected:
- Page processes login
- Redirects to dashboard
- Shows sidebar and main content area

## File Structure Check

Verify these files exist:

```bash
# Check public folder
docker-compose exec app ls -la /var/www/html/public/

# Should show:
# - index.html (main app)
# - app.js (JavaScript)
# - test.html (diagnostic page)

# Check API folder
docker-compose exec app ls -la /var/www/html/api/

# Should show:
# - index.php (API router)
# - diagnostics.php (diagnostic endpoint)

# Check root folder
docker-compose exec app ls -la /var/www/html/

# Should show:
# - .htaccess (Apache rules)
# - public/ (folder)
# - api/ (folder)
# - classes/ (folder)
```

## Browser Console Commands

You can run these in browser console (F12) to test:

```javascript
// Test API connectivity
fetch('/api/current-user')
  .then(r => r.json())
  .then(d => console.log(d));

// Test if app.js loaded
console.log(typeof API_BASE !== 'undefined' ? 'app.js loaded' : 'app.js NOT loaded');

// Check current user
console.log('Current user:', currentUser);
```

## Debug Mode

To enable detailed logging:

```bash
# View real-time logs
docker-compose logs -f app

# In another terminal, trigger actions in the app
# You'll see requests being logged
```

## Performance Check

If page is slow:

```bash
# Check Docker resource usage
docker stats

# Check if database is responding quickly
docker-compose exec mysql mysql -u leave_user -p leave_management -e "SELECT NOW();"

# Benchmark database
docker-compose exec mysql mysql -u leave_user -p leave_management -e "SELECT COUNT(*) FROM users;"
```

## Still Not Working?

1. **Check test.html**: http://localhost:8000/public/test.html
2. **Open F12 console**: Look for red errors
3. **Share logs**: `docker-compose logs > logs.txt`
4. **Check status**: `docker-compose ps`
5. **Review file structure**: Ensure all files exist
6. **Try fresh restart**: Run `fix-docker.sh`

---

**For detailed backend diagnostics, visit**: http://localhost:8000/public/test.html
