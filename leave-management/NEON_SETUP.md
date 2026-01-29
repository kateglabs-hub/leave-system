# Vercel Neon Postgres Quick Setup

Fast setup guide for deploying to Vercel with Neon PostgreSQL.

## 5-Minute Setup

### 1. Create Neon Database (2 minutes)
```bash
# Go to https://console.neon.tech
# Create account and new project
# Copy your connection string
# Should look like: postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require
```

### 2. Create Database Schema (2 minutes)
```bash
# Using your Neon connection string from above
psql "postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require" < database/schema.sql
psql "postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require" < database/seed.sql
```

Or use Neon's SQL Editor in the dashboard.

### 3. Deploy to Vercel (1 minute)
```bash
# Set environment variable
# In Vercel Dashboard → Settings → Environment Variables
# Add: DATABASE_URL = <your-neon-connection-string>

# Push and deploy
git push
vercel --prod
```

## Database URL Format

Neon URLs always include SSL:
```
postgresql://username:password@hostname/database?sslmode=require
```

Example:
```
postgresql://neondb_owner:AbCdEf123456@ep-blue-water-a1b2c3d4.us-east-1.neon.tech/neondb?sslmode=require
```

## Key Benefits of Neon

| Feature | Benefit |
|---------|---------|
| **Serverless** | Auto-scales with Vercel Functions |
| **Connection Pooling** | Handles concurrent serverless requests |
| **Free Tier** | Excellent for development/testing |
| **Branching** | Create isolated test databases |
| **Auto-suspend** | Saves costs with automatic sleep |

## Neon Pricing (as of 2026)

### Free Tier
- ✅ 3 GB storage
- ✅ 10 GB transfer
- ✅ 10 projects
- ✅ 100 connections per branch
- ✅ 7-day activity-based auto-suspend

### Pro Tier
- 💰 $0.16 per GiB/month storage
- 💰 $1 per 100 GiB transfer
- 💰 Up to 500 connections
- 💰 Dedicated support

## Connection String Location

In Neon Console:
1. Go to your project
2. Click "Connection details"
3. Copy the full URL including `?sslmode=require`
4. Paste as `DATABASE_URL` in Vercel

## Testing Local Connection

```bash
# Test Neon connection locally
psql "your-connection-string-here"

# Should connect successfully
leave_management=#
```

## Monitoring in Neon

Neon Dashboard shows:
- Query performance insights
- Storage usage
- Connection count
- Activity metrics

## Common Neon URLs by Region

```
# US East (default)
postgresql://user:pass@ep-xxxx.us-east-1.neon.tech/db

# US West
postgresql://user:pass@ep-xxxx.us-west-2.neon.tech/db

# EU West
postgresql://user:pass@ep-xxxx.eu-west-1.neon.tech/db

# Asia Pacific
postgresql://user:pass@ep-xxxx.ap-southeast-1.neon.tech/db
```

## Next Steps

1. ✅ Create Neon account
2. ✅ Get connection string
3. ✅ Run schema.sql
4. ✅ Set DATABASE_URL in Vercel
5. ✅ Deploy to Vercel
6. ✅ Monitor in Neon console

---

For full details, see [POSTGRESQL_MIGRATION.md](POSTGRESQL_MIGRATION.md)
