# Docker Setup Checklist

## ✅ Pre-Flight Check

Before starting, ensure you have:

- [ ] Docker Desktop installed
  - [Windows](https://docs.docker.com/desktop/install/windows-install/)
  - [Mac](https://docs.docker.com/desktop/install/mac-install/)
  - [Linux](https://docs.docker.com/desktop/install/linux-install/)
- [ ] At least 2GB free disk space
- [ ] Docker daemon running (you should see Docker icon in system tray)
- [ ] Clone or have the repository ready

---

## 🚀 Quick Setup (3 minutes)

### Step 1: Navigate to Project
```bash
cd leave-management
```

### Step 2: Copy Environment File
```bash
# Linux/Mac
cp .env.docker .env

# Windows
copy .env.docker .env
```

### Step 3: Start Services

**Option A: Automated (Recommended)**
```bash
# Linux/Mac
./docker-start.sh

# Windows
docker-start.bat
```

**Option B: Manual**
```bash
docker-compose up -d
```

### Step 4: Wait for Initialization
Wait 15-20 seconds for MySQL to be ready.

### Step 5: Verify All Running
```bash
docker-compose ps
```

All three containers should show status: `Up`

### Step 6: Access Application
- **Web App**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
- **Email**: admin@company.com
- **Password**: admin123

---

## 🔧 Post-Setup Configuration

### Verify Services Working

```bash
# Check all containers are running
docker-compose ps

# View application logs
docker-compose logs app

# View MySQL logs
docker-compose logs mysql

# View PHPMyAdmin logs
docker-compose logs phpmyadmin
```

### Test Database Connection

```bash
# Access MySQL shell
docker-compose exec mysql mysql -u leave_user -p leave_management

# Inside MySQL, run:
SELECT * FROM users LIMIT 5;
EXIT;
```

---

## 📝 Daily Development Workflow

### Start Development Session
```bash
# Ensure Docker is running
docker ps

# Start services if not already running
docker-compose up -d

# View logs as you work
docker-compose logs -f
```

### Make Changes
- Edit any PHP or JavaScript files
- Changes appear immediately (hot reload)
- Refresh browser to see updates

### Check Database
- Visit http://localhost:8080 for PHPMyAdmin
- Or use command line:
  ```bash
  docker-compose exec mysql mysql -u leave_user -p leave_management
  ```

### End Development Session
```bash
# Stop services
docker-compose stop

# Or completely remove (keep data)
docker-compose down

# Or remove everything (start fresh next time)
docker-compose down -v
```

---

## 🆘 Troubleshooting Guide

### Issue: "docker: command not found"

**Cause**: Docker not installed  
**Fix**: Install [Docker Desktop](https://www.docker.com/products/docker-desktop)

### Issue: "Cannot connect to Docker daemon"

**Cause**: Docker daemon not running  
**Fix**: Start Docker Desktop and wait for it to fully load

### Issue: "Port 8000 already in use"

**Cause**: Another service using the port  
**Fix**: Edit `.env` and change `APP_PORT` to different number, then:
```bash
docker-compose down
docker-compose up -d
```

### Issue: "MySQL connection refused"

**Cause**: MySQL still initializing  
**Fix**: Wait 20 seconds and run:
```bash
docker-compose restart app
```

### Issue: "Cannot access http://localhost:8000"

**Cause**: Containers not running or not ready  
**Fix**:
```bash
docker-compose ps                    # Check status
docker-compose logs app              # View app logs
docker-compose logs mysql            # View MySQL logs
docker-compose restart               # Restart everything
```

### Issue: "Error: No such file or directory"

**Cause**: Not in correct directory or .env not created  
**Fix**:
```bash
# Ensure you're in the right directory
pwd                                  # Should end with 'leave-management'

# Create .env file
cp .env.docker .env

# Then start again
docker-compose up -d
```

### Issue: "Permission denied" on docker-start.sh

**Cause**: Shell script not executable  
**Fix**:
```bash
chmod +x docker-start.sh
./docker-start.sh
```

### Issue: "Disk space error"

**Cause**: Not enough free disk space  
**Fix**:
```bash
# Check disk usage
docker system df

# Clean up unused images/containers
docker system prune

# Or start fresh
docker-compose down -v
```

---

## 📊 Verification Checklist

After setup, verify everything is working:

```bash
# 1. Check containers
docker-compose ps
# Expected: 3 containers all showing "Up"

# 2. Test web app
curl http://localhost:8000
# Expected: HTML response

# 3. Test MySQL
docker-compose exec mysql mysql -u leave_user -p leave_management -e "SELECT 1"
# Expected: No error, returns 1

# 4. Check logs for errors
docker-compose logs
# Expected: No ERROR messages (WARNINGs are OK)

# 5. Access PHPMyAdmin
# Open http://localhost:8080
# Expected: PHPMyAdmin login page loads

# 6. Login to app
# Open http://localhost:8000
# Email: admin@company.com
# Password: admin123
# Expected: Dashboard loads
```

---

## 🛑 How to Stop & Cleanup

### Stop Services (Keep Data)
```bash
docker-compose stop
```

### Remove Services (Keep Data)
```bash
docker-compose down
```

### Remove Everything (Fresh Start)
```bash
# WARNING: This deletes the database!
docker-compose down -v
```

### Clean All Docker (System Cleanup)
```bash
# Remove unused containers, networks, images
docker system prune

# More aggressive cleanup
docker system prune -a --volumes
```

---

## 📚 Useful Commands Reference

### View & Debug

| Command | Purpose |
|---------|---------|
| `docker-compose ps` | Show container status |
| `docker-compose logs` | View all logs |
| `docker-compose logs app` | View app logs only |
| `docker-compose logs -f` | Follow logs (live) |
| `docker-compose logs --tail=50` | Last 50 lines |

### Execute

| Command | Purpose |
|---------|---------|
| `docker-compose exec app bash` | Shell in app container |
| `docker-compose exec mysql bash` | Shell in MySQL container |
| `docker-compose exec app php -v` | Check PHP version |
| `docker-compose exec mysql mysql -u leave_user -p leave_management` | MySQL shell |

### Manage

| Command | Purpose |
|---------|---------|
| `docker-compose up -d` | Start services |
| `docker-compose down` | Stop services |
| `docker-compose restart` | Restart all services |
| `docker-compose restart app` | Restart app only |
| `docker-compose build` | Rebuild images |
| `docker-compose pull` | Pull latest images |

---

## 🎓 Learning Resources

- **Docker Official**: https://docs.docker.com/
- **Docker Compose**: https://docs.docker.com/compose/
- **PHP Docker**: https://hub.docker.com/_/php
- **MySQL Docker**: https://hub.docker.com/_/mysql

---

## ✨ Pro Tips

1. **Keep logs visible**: Run `docker-compose logs -f` in separate terminal while developing

2. **Database backups**: Export data before running `docker-compose down -v`
   ```bash
   docker-compose exec mysql mysqldump -u leave_user -p leave_management > backup.sql
   ```

3. **Different ports**: Change ports in `.env` if multiple projects use Docker

4. **Resource limits**: Edit `docker-compose.yml` to limit CPU/memory if needed

5. **Network debugging**: Services talk via service name (mysql, app, phpmyadmin)

6. **Fast rebuild**: Use `--no-cache` to rebuild without cache
   ```bash
   docker-compose build --no-cache
   ```

---

## 🔗 Quick Links

- [Full Docker Guide](./DOCKER.md)
- [Quick Reference](./DOCKER_QUICK_REF.md)
- [Implementation Details](./DOCKER_SETUP.md)
- [Main README](./README.md)
- [Deployment Guide](./DEPLOYMENT.md)

---

## ❓ Questions?

1. Check [DOCKER.md](./DOCKER.md) - most questions are answered there
2. Look at container logs: `docker-compose logs`
3. Try fresh start: `docker-compose down -v && docker-compose up -d`
4. Verify setup: Run `./verify-docker.sh` (Linux/Mac)

---

**Status**: ✅ Ready to Use  
**Last Updated**: January 28, 2026  
**Version**: 1.0.0
