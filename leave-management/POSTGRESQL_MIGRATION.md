# PostgreSQL Migration Guide

Your Leave Management System has been successfully migrated from MySQL to PostgreSQL.

## What Changed

### 1. **Database Configuration** (`config/database.php`)
- Changed from MySQL PDO driver to PostgreSQL (pgsql)
- Added `DB_PORT` environment variable (default: 5432)
- Updated connection string format for PostgreSQL

### 2. **Database Schema** (`database/schema.sql`)
- Converted `INT AUTO_INCREMENT` → `SERIAL`
- Converted `ENUM()` → `VARCHAR()` with `CHECK` constraints
- Converted `TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE` → `TIMESTAMP WITH TIME ZONE`
- Updated `INDEX` syntax to PostgreSQL format
- Updated foreign key constraints

### 3. **Seed Data** (`database/seed.sql`)
- Converted `YEAR(NOW())` → `EXTRACT(YEAR FROM NOW())::INT`

### 4. **Environment Variables** (`.env` & `vercel.json`)
- Updated `DB_HOST` from `mysql` to `postgres` (Docker)
- Added `DB_PORT: 5432` (PostgreSQL default)
- Updated Vercel environment variables

## Environment Variables for Deployment

### Local Development (.env)
```env
DB_HOST=postgres
DB_PORT=5432
DB_NAME=leave_management
DB_USER=leave_user
DB_PASSWORD=leave_password
```

### Production (Vercel/Railway)
Set these in your deployment platform:
- `DB_HOST` - Your PostgreSQL host (e.g., `db.railway.app`)
- `DB_PORT` - PostgreSQL port (usually `5432`)
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASSWORD` - Database password

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

## Deployment to Vercel

### 1. Connect Your Database
Use one of these services that offer PostgreSQL hosting:
- **Railway.app** (recommended - simple setup)
- **Vercel Postgres** (new offering)
- **Supabase** (PostgreSQL alternative)
- **Neon** (serverless PostgreSQL)

### 2. Set Vercel Environment Variables
In Vercel Dashboard → Project Settings → Environment Variables:
```
DB_HOST: <your-postgres-host>
DB_PORT: 5432
DB_NAME: <database-name>
DB_USER: <database-user>
DB_PASSWORD: <database-password>
```

### 3. Deploy
```bash
git add .
git commit -m "Migrate to PostgreSQL"
git push
vercel --prod
```

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

### Connection Error
- Ensure PostgreSQL is running
- Check `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD` in `.env`
- Verify PostgreSQL is listening on port 5432

### Schema Creation Error
- Run schema and seed separately if combined fails
- Check for syntax errors in SQL files
- Ensure database user has sufficient permissions

### Vercel Deployment 404
- Verify all environment variables are set in Vercel
- Check database can be accessed from Vercel servers
- Review Vercel build logs for errors

## Rollback to MySQL (if needed)

1. Restore backup database from MySQL
2. Revert changes:
   - `config/database.php` - change back to mysql driver
   - `database/schema.sql` - convert back to MySQL syntax
   - `.env` - change `DB_HOST=mysql` and `DB_PORT=3306`
3. Update `vercel.json` environment variables

---

**Migration completed successfully!** Your system is now ready for PostgreSQL.
