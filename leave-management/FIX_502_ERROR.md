# 🔧 Fix 502 Error - Manual Instructions

Your Docker containers are having issues. Here's how to fix them:

## Quick Fix (Run These Commands)

```bash
# Navigate to project
cd /workspaces/codespaces-blank/leave-management

# Step 1: Stop and remove all containers and volumes
docker-compose down -v

# Step 2: Rebuild with fresh configuration
docker-compose up -d --build

# Step 3: Wait 30 seconds for services to initialize
sleep 30

# Step 4: Check container status
docker-compose ps
# You should see all 3 containers with status "Up"

# Step 5: View application logs
docker-compose logs app

# Step 6: View MySQL logs
docker-compose logs mysql
```

## What Changed

The Docker configuration was updated to:
1. ✅ Use `mariadb-client-compat` instead of unavailable `mysql-client`
2. ✅ Simplified Apache configuration (removed overly complex rewrites)
3. ✅ Proper order of operations (permissions set before config copy)
4. ✅ Added health check to monitor container status
5. ✅ Improved error logging for debugging

## Access Your Application

After containers are running (all showing "Up"):

- **Web App**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
- **Email**: admin@company.com
- **Password**: admin123

## Verify Everything Works

```bash
# Check all containers are running and healthy
docker-compose ps

# View application logs (should see "Apache/2.4.x" and no errors)
docker-compose logs app | tail -20

# Test the web app is responding
curl http://localhost:8000

# Test the API is responding
curl http://localhost:8000/api/current-user
```

## Still Getting 502 Error?

### Check container logs for errors:
```bash
docker-compose logs app
```

Look for PHP errors or Apache errors. Common issues:
- Database connection failed → MySQL not ready
- Class not found → Wrong paths
- Parse error → PHP syntax error

### Restart just the app container:
```bash
docker-compose restart app
```

### Check if database is healthy:
```bash
docker-compose logs mysql
```

### Start fresh (this will delete data):
```bash
docker-compose down -v
docker-compose up -d --build
sleep 30
docker-compose ps
```

## Files Changed

The following Docker files were updated to fix the 502 issue:

1. **Dockerfile** - Fixed dependency issues and order of operations
2. **apache-vhost.conf** - Simplified Apache configuration  
3. **fix-docker.sh** - New script to rebuild and troubleshoot

## Need Help?

1. Run `docker-compose logs` to see all logs
2. Check [DOCKER.md](./DOCKER.md) troubleshooting section
3. Review [DOCKER_CHECKLIST.md](./DOCKER_CHECKLIST.md)

---

**Once containers are running and healthy, your app will be available at http://localhost:8000** 🚀
