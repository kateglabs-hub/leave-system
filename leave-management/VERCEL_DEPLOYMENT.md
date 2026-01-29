# Vercel Deployment Setup Guide

## Fix: Environment Variables Not Recognized

The error "Environment Variable 'DB_HOST' references Secret 'db_host', which does not exist" occurs when:
- ❌ vercel.json contains invalid secret references
- ❌ Environment variables not set in Vercel dashboard
- ❌ Using old `DB_*` variable names

## Solution

### 1. **Update vercel.json** ✅ (Already done)
Removed the `env` section with invalid secret references. Vercel will automatically inject environment variables from your project settings.

### 2. **Set Environment Variables in Vercel Dashboard**

**Go to:** https://vercel.com → Your Project → Settings → Environment Variables

**Option A: Using DATABASE_URL (Recommended)**
```
Name: DATABASE_URL
Value: postgresql://username:password@ep-xxxx.region.neon.tech/dbname?sslmode=require
Environments: Production, Preview, Development
```

**Option B: Using Individual POSTGRES_* Variables**
```
Name: POSTGRES_HOST
Value: ep-xxxx.neon.tech

Name: POSTGRES_PORT
Value: 5432

Name: POSTGRES_DATABASE
Value: neondb

Name: POSTGRES_USER
Value: neon_user

Name: POSTGRES_PASSWORD
Value: ••••••••••

Name: APP_ENV
Value: production

Name: APP_DEBUG
Value: false
```

### 3. **Verify Configuration**

Your `config/database.php` automatically supports:
1. **DATABASE_URL** (preferred)
   - Auto-parsed from connection string
   - SSL included
   - Connection pooling enabled

2. **POSTGRES_* variables** (fallback)
   - Individual configuration
   - Matches Vercel Neon standard naming
   - Manual SSL required

3. **DB_* variables** (legacy fallback)
   - For backward compatibility

### 4. **Deployment Steps**

```bash
# 1. Commit changes
git add .
git commit -m "Remove vercel.json env section, use dashboard variables"

# 2. Push to GitHub
git push

# 3. Vercel will automatically redeploy

# 4. Check deployment logs
# In Vercel dashboard: Deployments → Click latest → View logs
```

## Troubleshooting

### Still Getting "Secret Does Not Exist"
1. Check vercel.json has NO `"env"` section ✓
2. Verify environment variables in Vercel dashboard (all caps)
3. Redeploy: Settings → Redeploy

### Connection Refused
- Check DATABASE_URL is correct
- Verify Neon database is active
- Test locally first: `DATABASE_URL=... php app.php`

### "Unknown Environment Variable" Error
- Variables must be set in Vercel dashboard
- Case-sensitive: `DATABASE_URL` not `database_url`
- After setting, redeploy the project

## Quick Checklist

- ✅ vercel.json has NO `env` section
- ✅ DATABASE_URL or POSTGRES_* variables set in Vercel
- ✅ Neon database created and active
- ✅ Connection string copied correctly
- ✅ Project redeployed after env changes

## Files Updated

- ✅ `vercel.json` - Removed invalid env references
- ✅ `config/database.php` - Already supports all formats

---

**Your Vercel deployment should now work!** 🚀
