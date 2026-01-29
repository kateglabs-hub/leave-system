#!/bin/bash
# Docker Compose setup and cleanup script

set -e  # Exit on error

echo "🧹 Cleaning up Docker environment..."

# Stop all containers
echo "Stopping containers..."
docker-compose down --remove-orphans 2>/dev/null || true

# Kill any process using port 8080 and 8000
echo "Freeing up ports..."
lsof -ti:8080 | xargs kill -9 2>/dev/null || true
lsof -ti:8000 | xargs kill -9 2>/dev/null || true
lsof -ti:5432 | xargs kill -9 2>/dev/null || true

sleep 2

# Remove orphan containers explicitly
echo "Removing orphan containers..."
docker rm -f leave_management_mysql 2>/dev/null || true
docker rm -f leave_management_phpmyadmin 2>/dev/null || true

# Rebuild and start
echo "🚀 Starting Docker Compose..."
docker-compose up -d --build

# Wait for services
echo "⏳ Waiting for services to start (60 seconds)..."
sleep 60

# Check status
echo ""
echo "✅ Container Status:"
docker-compose ps

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Access the app:"
echo "  Frontend: http://localhost:8000"
echo "  pgAdmin:  http://localhost:8080"
echo ""
echo "Default credentials:"
echo "  Email: admin@company.com"
echo "  Password: admin123"
