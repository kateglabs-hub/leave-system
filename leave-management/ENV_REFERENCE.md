# Environment Variables Reference

## Vercel Production (.env.vercel)

Set these in Vercel Dashboard → Settings → Environment Variables

```env
# Required for Neon PostgreSQL
DATABASE_URL=postgresql://username:password@ep-xxxx.region.neon.tech/database?sslmode=require

# Optional application settings
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=UTC
SESSION_LIFETIME=7200
```

## Local Development (.env.local)

For local Docker or Neon testing:

```env
# Option 1: Neon (for testing against production database)
DATABASE_URL=postgresql://username:password@ep-xxxx.region.neon.tech/database?sslmode=require

# Option 2: Docker PostgreSQL (local only)
# DATABASE_URL=postgresql://leave_user:leave_password@localhost:5432/leave_management

# Application settings
APP_ENV=development
APP_DEBUG=true
APP_TIMEZONE=UTC
SESSION_LIFETIME=7200
```

## Docker Compose (.env.docker)

```env
# PostgreSQL container
POSTGRES_USER=leave_user
POSTGRES_PASSWORD=leave_password
POSTGRES_DB=leave_management
POSTGRES_INITDB_ARGS=-c "shared_preload_libraries=pg_stat_statements"

# PHP Application
APP_ENV=development
APP_DEBUG=true
APP_PORT=8000
DB_HOST=postgres
DB_PORT=5432
```

## Getting Your Neon Connection String

1. Go to https://console.neon.tech
2. Select your project
3. Click "Connection details"
4. Copy the connection string
5. Format: `postgresql://user:password@ep-xxxx.region.neon.tech/dbname?sslmode=require`

## Variable Explanation

| Variable | Purpose | Example |
|----------|---------|---------|
| `DATABASE_URL` | Full connection string | `postgresql://...` |
| `DB_HOST` | Database host (if using individual vars) | `ep-xxxx.neon.tech` |
| `DB_PORT` | Database port (if using individual vars) | `5432` |
| `DB_NAME` | Database name (if using individual vars) | `leave_management` |
| `DB_USER` | Database username (if using individual vars) | `neon_user` |
| `DB_PASSWORD` | Database password (if using individual vars) | `*****` |
| `APP_ENV` | Environment mode | `production` or `development` |
| `APP_DEBUG` | Debug mode (use false in production) | `true` or `false` |
| `APP_TIMEZONE` | Server timezone | `UTC` |
| `SESSION_LIFETIME` | Session duration in seconds | `7200` (2 hours) |

## Using DATABASE_URL vs Individual Variables

### Recommended: DATABASE_URL (Neon Optimized)
- ✅ Single environment variable
- ✅ Automatic SSL support
- ✅ Connection pooling ready
- ✅ No additional parsing needed

### Alternative: Individual Variables (Docker Compatible)
- ✅ Easier to manage per environment
- ✅ Better for complex setups
- ⚠️ Requires manual SSL configuration
- ⚠️ Not pooling-optimized

## Never Commit These Files

Add to `.gitignore`:
```
.env
.env.local
.env.*.local
.env.production
.env.development
```

## Security Best Practices

✅ **DO:**
- Use strong passwords for database
- Never commit `.env` files
- Rotate passwords regularly
- Use Neon's IP allowlist in paid plans
- Enable two-factor authentication on Neon account

❌ **DON'T:**
- Hardcode credentials in code
- Share connection strings publicly
- Use same password across environments
- Copy production credentials to development
- Log or debug production credentials

## Vercel Dashboard Steps

1. Go to your Vercel project
2. Click "Settings"
3. Click "Environment Variables" (left sidebar)
4. Click "Add New"
5. Name: `DATABASE_URL`
6. Value: `postgresql://...` (your Neon connection string)
7. Select "Production" (or appropriate environment)
8. Click "Save"
9. Redeploy your application

## Testing Your Connection

```bash
# Test Neon connection locally
psql "postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require"

# You should see:
# psql (15.x.x, server 15.x)
# "dbname"=#
```

## Troubleshooting Connection Issues

**Error: "could not translate host name"**
- Check hostname in connection string
- Verify Neon project is active

**Error: "invalid password"**
- Double-check password in connection string
- Password should be URL-encoded if it contains special characters

**Error: "database does not exist"**
- Verify database name at end of URL
- May need to create database first

**Error: "SSL connection error"**
- Always use `?sslmode=require` with Neon
- Config file handles this automatically
