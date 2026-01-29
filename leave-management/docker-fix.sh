#!/bin/bash

# Quick fix for Docker issues
# Run from: cd leave-management && bash docker-fix.sh

echo "🔧 Fixing Docker issues..."

# Stop containers gracefully
echo "Stopping containers..."
docker-compose down 2>/dev/null || true

echo "Removing orphan containers..."
docker rm -f leave_management_mysql leave_management_phpmyadmin 2>/dev/null || true

echo "Freeing ports 5432, 8000, 8080..."
# Kill processes using these ports
for port in 5432 8000 8080; do
    pids=$(lsof -ti:$port 2>/dev/null || true)
    if [ ! -z "$pids" ]; then
        echo "Killing process on port $port: $pids"
        kill -9 $pids 2>/dev/null || true
    fi
done

sleep 3

echo "🚀 Starting fresh..."
docker-compose up -d --build

echo ""
echo "⏳ Waiting 60 seconds for services to initialize..."
sleep 60

echo ""
echo "📊 Checking status..."
docker-compose ps

echo ""
echo "✅ Ready!"
echo ""
echo "Access the app:"
echo "  🌐 Frontend: http://localhost:8000"
echo "  🗄️  pgAdmin:  http://localhost:8080"
echo ""
echo "Login with:"
echo "  📧 Email: admin@company.com"
echo "  🔑 Password: admin123"
