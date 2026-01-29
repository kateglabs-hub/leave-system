# Running the Leave Management System Locally

## Option 1: Docker Compose (Recommended)

### Quick Start
```bash
cd leave-management

# Clean up old containers (if needed)
docker-compose down --remove-orphans

# Start all services
docker-compose up -d

# Wait for services to be ready (30-60 seconds)

# Access the app
# Frontend: http://localhost:8000
# pgAdmin: http://localhost:8080
```

### Services Running
- **PHP Application**: http://localhost:8000
- **PostgreSQL Database**: localhost:5432
- **pgAdmin Console**: http://localhost:8080
  - Email: admin@example.com
  - Password: admin

### Logs
```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app        # PHP app
docker-compose logs -f postgres   # Database
docker-compose logs -f pgadmin    # Database admin
```

### Stop Services
```bash
# Stop all services
docker-compose down

# Remove volumes (WARNING: deletes database)
docker-compose down -v
```

---

## Option 2: PHP Built-in Server (Local Development)

### Prerequisites
```bash
# Install PHP (if not already installed)
# Ubuntu/Debian
sudo apt-get install php php-pgsql php-pdo

# macOS
brew install php

# Windows
# Download from https://www.php.net/downloads
```

### Start Local Server
```bash
cd leave-management/public

# Start PHP development server
php -S localhost:8000

# Access: http://localhost:8000
```

**Note**: This requires a separate PostgreSQL installation or Neon connection.

---

## Option 3: Using Neon for Testing

If you want to test against Neon PostgreSQL:

1. Set `DATABASE_URL` in `.env`:
```env
DATABASE_URL=postgresql://user:pass@ep-xxxx.neon.tech/dbname?sslmode=require
```

2. Run with PHP dev server:
```bash
cd leave-management/public
php -S localhost:8000
```

3. The app will connect to your Neon database automatically

---

## Testing the Application

### Admin Login
- Email: `admin@company.com`
- Password: `admin123`

### Features to Test
1. **Login**
   - Test with admin credentials
   - Check session management

2. **Leave Management**
   - Submit leave request
   - Check leave balance
   - Review leave types

3. **Admin Functions**
   - Approve/reject requests
   - View all leave requests
   - Check reports

4. **Database**
   - Check database in pgAdmin
   - View created tables
   - Review inserted data

### API Endpoints
```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@company.com","password":"admin123"}'

# Get leave balance
curl http://localhost:8000/api/get-balance

# Submit leave request
curl -X POST http://localhost:8000/api/request-leave \
  -H "Content-Type: application/json" \
  -d '{
    "leave_type_id":1,
    "start_date":"2026-02-01",
    "end_date":"2026-02-05",
    "reason":"Vacation"
  }'
```

---

## Database Connection

### PostgreSQL Details
- **Host**: postgres (Docker) or localhost
- **Port**: 5432
- **Database**: leave_management
- **User**: leave_user
- **Password**: leave_password

### Connect with psql
```bash
# Via Docker
docker-compose exec postgres psql -U leave_user -d leave_management

# Or directly (if PostgreSQL installed)
psql -h localhost -U leave_user -d leave_management
```

### pgAdmin Access (Docker)
1. Go to http://localhost:8080
2. Login: admin@example.com / admin
3. Add server:
   - Host: postgres
   - Port: 5432
   - User: leave_user
   - Password: leave_password

---

## Troubleshooting

### Docker Issues

**Port Already in Use**
```bash
# Find and stop container using port
lsof -i :8000
kill -9 <PID>

# Or use different port
# Update docker-compose.yml APP_PORT or PHPMYADMIN_PORT
```

**Containers Won't Start**
```bash
# Check logs
docker-compose logs

# Rebuild images
docker-compose up -d --build

# Remove and restart
docker-compose down -v
docker-compose up -d
```

**Database Connection Error**
```bash
# Ensure PostgreSQL container is ready
docker-compose logs postgres

# Wait for healthcheck to pass
docker-compose ps  # Check STATUS
```

### PHP Local Server Issues

**PHP Not Found**
```bash
# Install PHP first
# Then try: php -S localhost:8000
```

**Port 8000 In Use**
```bash
# Use different port
php -S localhost:9000
```

**Cannot Connect to Database**
```bash
# Check DATABASE_URL in .env
# Verify PostgreSQL is running (Docker or local)
# Check credentials match
```

---

## Environment Variables

### Docker (.env)
```env
POSTGRES_HOST=postgres
POSTGRES_PORT=5432
POSTGRES_DATABASE=leave_management
POSTGRES_USER=leave_user
POSTGRES_PASSWORD=leave_password
APP_ENV=development
APP_DEBUG=true
```

### Neon (.env)
```env
DATABASE_URL=postgresql://user:pass@ep-xxxx.region.neon.tech/dbname?sslmode=require
APP_ENV=development
APP_DEBUG=true
```

---

## Performance Testing

### Load Testing
```bash
# Install Apache Bench (if not available)
sudo apt-get install apache2-utils  # Linux
brew install httpd  # macOS

# Test login endpoint
ab -n 100 -c 10 http://localhost:8000/
```

### Query Performance
- Check pgAdmin for slow queries
- Review Neon monitoring (if using Neon)
- Enable query logging in database config

---

## Next Steps

After successful local testing:

1. Commit changes: `git add . && git commit -m "Test local setup"`
2. Push to GitHub: `git push`
3. Deploy to Vercel with Neon
4. Monitor logs and performance

See [NEON_SETUP.md](NEON_SETUP.md) for Vercel deployment.
