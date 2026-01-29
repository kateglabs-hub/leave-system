# PostgreSQL Migration Guide (Vercel Neon Optimized)

Your Leave Management System has been successfully migrated from MySQL to PostgreSQL with optimization for **Vercel Neon**.

## What Changed

### 1. **Database Configuration** (`config/database.php`)
- Changed from MySQL PDO driver to PostgreSQL (pgsql)
- Added support for **Neon DATABASE_URL** format (single connection string)
- Added support for individual environment variables (local development)
- Added SSL support (required for Neon in production)
- Added connection pooling configuration
- Added automatic sslmode=require for Neon connections

### 2. **Database Schema** (`database/schema.sql`)
- Converted `INT AUTO_INCREMENT` → `SERIAL`
- Converted `ENUM()` → `VARCHAR()` with `CHECK` constraints
- Converted `TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE` → `TIMESTAMP WITH TIME ZONE`
- Updated `INDEX` syntax to PostgreSQL format
- Updated foreign key constraints

### 3. **Seed Data** (`database/seed.sql`)
- Converted `YEAR(NOW())` → `EXTRACT(YEAR FROM NOW())::INT`

### 4. **Environment Variables** (`.env` & `vercel.json`)
- Updated to use **Neon DATABASE_URL** single connection string (recommended)
- Optional: Individual environment variables for local Docker development
- `vercel.json` now uses single `@database_url` environment variable

## Environment Variables for Deployment

### Local Development (.env)
**Option 1: Neon (for testing connection)**
```env
DATABASE_URL=postgresql://user:password@ep-xxxx-region.neon.tech/dbname?sslmode=require
```

**Option 2: Docker (local testing)**
```env
DB_HOST=postgres
DB_PORT=5432
DB_NAME=leave_management
DB_USER=leave_user
DB_PASSWORD=leave_password
```

### Production (Vercel + Neon)
Set **only one** environment variable in Vercel:
- `DATABASE_URL` - Your Neon connection string from https://console.neon.tech

Example Neon URL format:
```
postgresql://neon_user:password@ep-cool-moon-12345.us-east-1.neon.tech/neon_db?sslmode=require
```

## Docker Setup (PostgreSQL)

Update your `docker-compose.yml` to use PostgreSQL:

```yaml
version: '3.8'
services:
  postgres:
    image: postgres:15-alpine
    environment:
      POSTGRES_USER: leave_user
      POSTGRES_PASSWORD: leave_password
      POSTGRES_DB: leave_management
    ports:
      - "5432:5432"
    volumes:
      - postgres_data:/var/lib/postgresql/data
      - ./database/schema.sql:/docker-entrypoint-initdb.d/01-schema.sql
      - ./database/seed.sql:/docker-entrypoint-initdb.d/02-seed.sql

  php:
    build: .
    ports:
      - "8000:8000"
    depends_on:
      - postgres
    environment:
      DB_HOST: postgres
      DB_PORT: 5432
      DB_NAME: leave_management
      DB_USER: leave_user
      DB_PASSWORD: leave_password

volumes:
  postgres_data:
```

## Database Creation

### Using PostgreSQL Command Line
```bash
# Connect to PostgreSQL
psql -U postgres

# Create database
CREATE DATABASE leave_management;

# Connect to the database
\c leave_management

# Run the schema
\i database/schema.sql

# Insert default leave types and departments
INSERT INTO leave_types (name, description, days_allowed_staff, days_allowed_management, requires_documentation, is_paid) VALUES
('Annual Leave', 'Regular annual vacation leave', 21, 25, FALSE, TRUE),
('Sick Leave', 'Medical leave for illness', 10, 15, TRUE, TRUE),
('Compassionate Leave', 'Leave for family emergencies or bereavement', 5, 7, TRUE, TRUE),
('Maternity Leave', 'Maternity leave for mothers', 90, 90, TRUE, TRUE),
('Paternity Leave', 'Paternity leave for fathers', 10, 14, TRUE, TRUE),
('Study Leave', 'Leave for educational purposes', 5, 10, TRUE, FALSE),
('Unpaid Leave', 'Leave without pay', 0, 0, TRUE, FALSE);

INSERT INTO departments (name, description) VALUES
('Human Resources', 'HR Department'),
('Information Technology', 'IT Department'),
('Finance', 'Finance Department'),
('Marketing', 'Marketing Department'),
('Operations', 'Operations Department'),
('Sales', 'Sales Department');

# Run seed
\i database/seed.sql
```

### Using PgAdmin
1. Right-click on "Databases" and create new database
2. Import schema from `database/schema.sql`
3. Insert seed data from `database/seed.sql`

## Deployment to Vercel with Neon

### 1. Create Neon Database
1. Go to https://console.neon.tech
2. Sign up / Log in
3. Create a new project (free tier available)
4. Copy your connection string (looks like `postgresql://user:password@ep-xxxx.neon.tech/dbname?sslmode=require`)

### 2. Set Vercel Environment Variable
In Vercel Dashboard → Project Settings → Environment Variables:
```
DATABASE_URL: <your-neon-connection-string>
```

Example:
```
postgresql://neon_user:abc123def456@ep-cool-moon-12345.us-east-1.neon.tech/neon_db?sslmode=require
```

### 3. Create Database Schema on Neon
Connect to Neon and run the schema:

**Option A: Using psql**
```bash
# Install psql if needed
# brew install postgresql  (macOS)
# apt-get install postgresql-client  (Linux)

psql DATABASE_URL < database/schema.sql
psql DATABASE_URL < database/seed.sql
```

**Option B: Using Neon Console**
1. Go to your Neon project
2. Click "SQL Editor"
3. Copy-paste contents from `database/schema.sql`
4. Run the query
5. Repeat for `database/seed.sql`

**Option C: Using pgAdmin**
1. Connect pgAdmin to Neon using the connection string
2. Import `database/schema.sql`
3. Import `database/seed.sql`

### 4. Deploy
```bash
git add .
git commit -m "Optimize for Vercel Neon PostgreSQL"
git push
vercel --prod
```

## Neon-Specific Features

### Connection Pooling
The database config automatically enables connection pooling when using Neon. This:
- Reduces connection overhead
- Improves performance on serverless (Vercel Functions)
- Handles concurrent requests efficiently

### SSL/TLS
Neon requires SSL connections. The config automatically:
- Adds `sslmode=require` when using DATABASE_URL
- Adds `sslmode=require` in production environments
- Enforces secure connections to Neon

### Automatic Region Selection
Neon automatically selects the closest region. Connection URLs vary:
- US East: `ep-xxxx.us-east-1.neon.tech`
- EU West: `ep-xxxx.eu-west-1.neon.tech`
- Asia Pacific: `ep-xxxx.ap-southeast-1.neon.tech`

## Differences Between MySQL and PostgreSQL

| Feature | MySQL | PostgreSQL |
|---------|-------|-----------|
| Auto Increment | `INT AUTO_INCREMENT` | `SERIAL` |
| ENUM | Native `ENUM()` type | Use `VARCHAR` + `CHECK` |
| Current Timestamp | `CURRENT_TIMESTAMP` | `CURRENT_TIMESTAMP` |
| Auto Update | `ON UPDATE CURRENT_TIMESTAMP` | Manual or trigger |
| Year Function | `YEAR()` | `EXTRACT(YEAR FROM)` |
| NULL Timestamp | `TIMESTAMP NULL` | `TIMESTAMP` nullable |

## Troubleshooting

### Neon Connection Errors
**Error: "sslmode not allowed in connection string"**
- Ensure your Neon URL includes `?sslmode=require` at the end
- The config handles this automatically if DATABASE_URL is set

**Error: "Connection refused"**
- Check Neon project is active (may pause after 7 days of inactivity in free tier)
- Verify DATABASE_URL environment variable is set in Vercel
- Check database credentials are correct

**Error: "database doesn't exist"**
- Run the schema.sql against your Neon database
- Ensure database name in URL matches created database

### Vercel Deployment 404
- Verify `DATABASE_URL` is set in Vercel environment
- Check build logs in Vercel dashboard
- Ensure schema was created before deployment

### Performance Issues
- Enable query logging in Neon console to identify slow queries
- Use Neon's built-in monitoring
- Consider upgrading from free tier if hitting limits

### Connection Pooling Issues
- Neon automatically manages pooling
- Max connections per branch: 100 (free tier)
- Use separate branches for different environments
