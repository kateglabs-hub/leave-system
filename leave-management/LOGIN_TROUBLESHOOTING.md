# Vercel Login Troubleshooting

## Common Causes of Login Failure

### 1. **Database Not Initialized** (Most Common)
❌ Symptoms:
- Login page shows but login fails
- No error message or generic error
- "Invalid email or password"

✅ Solution:
You need to manually initialize the database with seed data.

**Steps:**
1. Go to your Neon dashboard: https://console.neon.tech
2. Select your project and database
3. Click "SQL Editor"
4. Run the schema script:
   - Copy contents of `database/schema.sql`
   - Execute in Neon SQL Editor
5. Run the seed script:
   - Copy contents of `database/seed.sql`
   - Execute in Neon SQL Editor

**Or use psql:**
```bash
psql "postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require" < database/schema.sql
psql "postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require" < database/seed.sql
```

**Verify data exists:**
```sql
SELECT * FROM users;
SELECT * FROM departments;
SELECT * FROM leave_types;
```

### 2. **Database Connection Issue**
❌ Symptoms:
- Blank page or 500 error
- Network tab shows failed API request
- Vercel logs show connection error

✅ Solution:
Check environment variables:

1. Go to Vercel → Project Settings → Environment Variables
2. Verify `DATABASE_URL` is set correctly:
   ```
   postgresql://username:password@ep-xxxx.neon.tech/dbname?sslmode=require
   ```
3. Check credentials match your Neon database
4. Redeploy: Settings → Redeploy

### 3. **API Route Issue**
❌ Symptoms:
- 404 error on `/api/login`
- Browser network tab shows 404 response

✅ Solution:
Verify `vercel.json` routing:

```json
{
  "version": 2,
  "builds": [
    {
      "src": "api/index.php",
      "use": "vercel-php@0.7.1"
    },
    {
      "src": "public/**",
      "use": "@vercel/static"
    }
  ],
  "routes": [
    {
      "src": "/api/(.*)",
      "dest": "/api/index.php"
    },
    {
      "src": "/public/(.*)",
      "dest": "/public/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/public/index.html"
    }
  ]
}
```

Push changes if different:
```bash
git add vercel.json
git commit -m "Verify API routing"
git push
```

### 4. **CORS/Session Issues**
❌ Symptoms:
- Login request succeeds but session not persisted
- Redirects back to login page after successful login
- "Not authenticated" error on next page load

✅ Solution:
For serverless (Vercel Functions), sessions don't persist by default.

**Option A: Use token-based auth (recommended)**
Modify Auth.php to return JWT token instead of session.

**Option B: Database session storage**
Implement session handler that stores in database instead of files.

### 5. **Frontend Issue**
❌ Symptoms:
- Login button doesn't respond
- Console shows JavaScript errors

✅ Solution:
Open browser DevTools (F12) → Console tab:
1. Check for any JavaScript errors
2. Check Network tab → click login → see the request/response
3. Look for API errors in response

### 6. **Password Incorrect**
❌ Symptoms:
- Know the credentials but still getting "Invalid email or password"

✅ Solution:
Default credentials from seed.sql:
- **Email**: `admin@company.com`
- **Password**: `admin123`

If these don't work, verify the user exists and password is correct:
```sql
SELECT id, email, password FROM users WHERE email = 'admin@company.com';
```

## Quick Diagnosis Steps

### Step 1: Check API Response
In browser, open DevTools (F12) → Network tab → Try login → Click the login request:
- Status should be 200
- Response should be JSON: `{"success": true, "user": {...}}`

### Step 2: Check Database Connection
Add temporary debug endpoint to `api/index.php`:
```php
if ($endpoint === 'health') {
    try {
        $stmt = $this->db->query("SELECT NOW()");
        echo json_encode(['success' => true, 'database' => 'connected']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}
```

Then test: `https://your-domain/api/health`

### Step 3: Check Environment Variables
In Vercel logs (Deployments → click latest → Logs), search for:
- Database connection attempts
- Any "undefined variable" errors

## Verify Your Setup

### Checklist:
- ✅ Neon database created at https://console.neon.tech
- ✅ DATABASE_URL set in Vercel environment variables
- ✅ Schema initialized (tables created)
- ✅ Seed data initialized (users created)
- ✅ vercel.json has correct routing
- ✅ Production redeploy completed
- ✅ Trying correct credentials: admin@company.com / admin123

## Testing Endpoints

### Direct API Testing:
```bash
# Test login
curl -X POST https://your-domain/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@company.com","password":"admin123"}'

# Should return:
# {"success":true,"user":{...}}

# Test current user
curl https://your-domain/api/current-user

# Should return 401 if not logged in (expected)
```

## Still Not Working?

1. **Check Vercel Logs**
   - Deployments → Latest → Logs
   - Search for: "error", "exception", "database"

2. **Check Neon Logs**
   - https://console.neon.tech
   - Project → Monitoring
   - Look for connection errors

3. **Enable Debug Mode**
   - Set `APP_DEBUG=true` in Vercel environment
   - Redeploy and check logs

4. **Reset Everything**
   - Drop and recreate Neon database
   - Re-run schema.sql and seed.sql
   - Verify admin user exists
   - Redeploy Vercel project

---

**Common Fix: Most login issues are solved by running the seed.sql script!**
