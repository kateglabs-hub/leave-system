# Docker Setup Guide

## 🐳 Running the Leave Management System with Docker

This guide walks you through setting up and running the entire application stack using Docker and Docker Compose.

### Prerequisites

- **Docker**: [Install Docker](https://docs.docker.com/get-docker/)
- **Docker Compose**: [Install Docker Compose](https://docs.docker.com/compose/install/)
- **Git**: For cloning the repository

### Quick Start (3 Steps)

#### 1. Clone and Setup Environment

```bash
# Clone the repository
git clone <repository-url>
cd leave-management

# Copy environment file for Docker
cp .env.docker .env
```

#### 2. Build and Start Containers

```bash
# Build images and start all services
docker-compose up -d

# Wait for MySQL to initialize (10-15 seconds)
docker-compose logs mysql
```

#### 3. Access the Application

- **Web Application**: [http://localhost:8000](http://localhost:8000)
- **PHPMyAdmin**: [http://localhost:8080](http://localhost:8080)

**Default Login Credentials:**
- Email: `admin@company.com`
- Password: `admin123`

---

## 📋 Services Overview

### Docker Compose Services

Your Docker setup includes three services:

#### 1. **MySQL Database** (Port 3306)
- Database: `leave_management`
- User: `leave_user`
- Password: `leave_password`
- Schema automatically imported from `database/schema.sql`

#### 2. **PHP Application** (Port 8000)
- Runs Apache with PHP 8.2
- Auto-reloads on file changes
- Mounts local files for live development

#### 3. **PHPMyAdmin** (Port 8080)
- Web-based database management
- Access: [http://localhost:8080](http://localhost:8080)
- Username: `leave_user` or `root`
- Password: See `.env` file

---

## 🛠️ Common Docker Commands

### Start Services
```bash
# Start all services in background
docker-compose up -d

# Start and view logs
docker-compose up

# Start specific service
docker-compose up -d mysql
docker-compose up -d app
```

### Stop Services
```bash
# Stop all services
docker-compose down

# Stop without removing volumes
docker-compose stop

# Stop and remove everything (including data)
docker-compose down -v
```

### View Logs
```bash
# View all logs
docker-compose logs

# Follow logs in real-time
docker-compose logs -f

# View specific service logs
docker-compose logs mysql
docker-compose logs app
```

### Execute Commands
```bash
# Run command in app container
docker-compose exec app bash

# Run PHP command
docker-compose exec app php --version

# Run MySQL command
docker-compose exec mysql mysql -u leave_user -p leave_management
```

### Rebuild Services
```bash
# Rebuild images
docker-compose build

# Rebuild and restart
docker-compose up -d --build
```

---

## 📝 Configuration

### Environment Variables (`.env`)

Edit `.env` to customize your setup:

```bash
# Database
DB_HOST=mysql              # Use "mysql" for Docker
DB_NAME=leave_management
DB_USER=leave_user
DB_PASSWORD=leave_password

# Application
APP_ENV=development
APP_DEBUG=true

# Ports
APP_PORT=8000
DB_PORT=3306
PHPMYADMIN_PORT=8080
```

### Docker Compose Override

For local development overrides, create `docker-compose.override.yml`:

```yaml
version: '3.8'
services:
  app:
    environment:
      APP_DEBUG: "true"
      APP_ENV: "development"
```

---

## 🔧 Troubleshooting

### Container Won't Start

```bash
# Check container status
docker-compose ps

# View error logs
docker-compose logs app
docker-compose logs mysql
```

### Port Already in Use

```bash
# Change ports in .env
APP_PORT=9000
PHPMYADMIN_PORT=9080

# Restart services
docker-compose up -d
```

### Database Connection Error

```bash
# Verify MySQL is running and healthy
docker-compose ps
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql

# Wait a few seconds for MySQL to initialize
sleep 15
```

### Need Fresh Database

```bash
# Remove data volume and restart
docker-compose down -v
docker-compose up -d

# Database will be re-imported automatically
```

### Reset Everything

```bash
# Remove all containers, networks, and volumes
docker-compose down -v

# Rebuild and start fresh
docker-compose up -d --build
```

---

## 📱 Development Workflow

### Hot Reload

The application container mounts your local files, so changes are reflected immediately:

```bash
# Edit your PHP files
vim classes/Leave.php

# Changes take effect instantly - no restart needed!
```

### Database Access

#### Via PHPMyAdmin (GUI)
1. Open [http://localhost:8080](http://localhost:8080)
2. Login with credentials from `.env`
3. Browse and manage tables visually

#### Via Command Line
```bash
# Access MySQL shell in container
docker-compose exec mysql mysql -u leave_user -p leave_management

# Run SQL commands
mysql> SELECT * FROM users;
```

### Install Dependencies

```bash
# Install PHP dependencies with Composer
docker-compose exec app composer install

# Update dependencies
docker-compose exec app composer update
```

---

## 🚀 Deployment

### To Production

For production deployment to Vercel or other platforms:

1. **Update `.env` for production:**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Use Vercel Configuration** (already configured in `vercel.json`)

3. **Deploy:**
   ```bash
   vercel --prod
   ```

See [DEPLOYMENT.md](./DEPLOYMENT.md) for detailed deployment instructions.

---

## 📚 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/compose-file/)
- [PHP Official Docker Images](https://hub.docker.com/_/php)
- [MySQL Official Docker Images](https://hub.docker.com/_/mysql)

---

## ✅ Verification Checklist

After startup, verify everything works:

- [ ] All containers running: `docker-compose ps`
- [ ] Web app accessible: http://localhost:8000
- [ ] PHPMyAdmin accessible: http://localhost:8080
- [ ] Can login with default credentials
- [ ] Database tables visible in PHPMyAdmin
- [ ] No errors in logs: `docker-compose logs`

---

## Support

For issues or questions:
1. Check logs: `docker-compose logs -f`
2. Review [CONTRIBUTING.md](./CONTRIBUTING.md)
3. Check existing issues in repository
4. Create detailed issue report

---

**Happy coding! 🎉**
