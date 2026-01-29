# Environment Variables Updated to Vercel Neon Standard

All environment variables have been updated to use **Vercel Neon's standard naming convention** (`POSTGRES_*`).

## Changes Made

### 1. **Environment Variables** (.env)
✅ Changed from `DB_*` to `POSTGRES_*`:
- `DB_HOST` → `POSTGRES_HOST`
- `DB_PORT` → `POSTGRES_PORT`
- `DB_NAME` → `POSTGRES_DATABASE`
- `DB_USER` → `POSTGRES_USER`
- `DB_PASSWORD` → `POSTGRES_PASSWORD`

### 2. **Database Config** (config/database.php)
✅ Updated to read `POSTGRES_*` variables with `DB_*` fallback:
```php
$this->host = getenv('POSTGRES_HOST') ?: getenv('DB_HOST') ?: 'localhost';
$this->port = getenv('POSTGRES_PORT') ?: getenv('DB_PORT') ?: '5432';
$this->db_name = getenv('POSTGRES_DATABASE') ?: getenv('DB_NAME') ?: 'leave_management';
$this->username = getenv('POSTGRES_USER') ?: getenv('DB_USER') ?: 'postgres';
$this->password = getenv('POSTGRES_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';
```

**Benefits:**
- ✅ Matches Vercel Neon's official environment variable names
- ✅ Maintains backward compatibility with `DB_*` variables
- ✅ Automatically supports Vercel deployments

### 3. **Docker Compose** (docker-compose.yml)
✅ Updated all environment variable references:
```yaml
environment:
  POSTGRES_USER: ${POSTGRES_USER:-leave_user}
  POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-leave_password}
  POSTGRES_DB: ${POSTGRES_DATABASE:-leave_management}
```

### 4. **Documentation**
✅ Updated all references in:
- RUN_LOCALLY.md
- ENV_REFERENCE.md
- NEON_SETUP.md

## For Vercel Deployment

### Set in Vercel Dashboard:
1. Go to **Settings → Environment Variables**
2. Add (choose one approach):

**Option A: Single CONNECTION STRING (Recommended)**
```
DATABASE_URL = postgresql://username:password@ep-xxxx.region.neon.tech/dbname?sslmode=require
```

**Option B: Individual Variables (Matches Neon's standard)**
```
POSTGRES_HOST = ep-xxxx.region.neon.tech
POSTGRES_PORT = 5432
POSTGRES_DATABASE = neondb
POSTGRES_USER = neondb_owner
POSTGRES_PASSWORD = ••••••
```

## Local Development

### Using Docker:
```bash
# Variables in .env are already set
docker-compose up -d
```

### Using Neon directly:
```env
DATABASE_URL=postgresql://user:password@ep-xxxx.neon.tech/database?sslmode=require
```

## Testing the Configuration

```bash
# Check config reads correct variables
php -r "require 'config/database.php'; \$db = new Database(); \$conn = \$db->getConnection(); echo 'Connected!';"
```

## Backward Compatibility

The code maintains backward compatibility with old `DB_*` variables:
- If `POSTGRES_*` variables exist, they take priority
- If not, falls back to `DB_*` variables
- If neither exist, uses defaults

This allows gradual migration without breaking existing setups.

## All Files Updated

✅ `.env` - Primary configuration
✅ `config/database.php` - Database connection
✅ `docker-compose.yml` - Docker services
✅ `RUN_LOCALLY.md` - Local development guide
✅ `ENV_REFERENCE.md` - Environment variable documentation

---

**Your app now uses Vercel Neon's standard naming convention!** 🚀
