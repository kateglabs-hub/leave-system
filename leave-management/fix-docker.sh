#!/bin/bash

# Docker Troubleshooting and Restart Script
# Run this to fix the 502 error

echo "🔧 Fixing Leave Management System Docker Setup..."
echo ""

# Step 1: Stop and remove everything
echo "Step 1: Stopping containers..."
docker-compose down -v

# Step 2: Rebuild with fresh configuration
echo ""
echo "Step 2: Building images fresh..."
docker-compose up -d --build

# Step 3: Wait for services
echo ""
echo "Step 3: Waiting for services to start (30 seconds)..."
sleep 30

# Step 4: Check status
echo ""
echo "Step 4: Checking container status..."
docker-compose ps

# Step 5: Show logs
echo ""
echo "Step 5: Application logs (last 30 lines)..."
docker-compose logs app | tail -30

echo ""
echo "✅ Setup complete!"
echo ""
echo "Access your application:"
echo "  Web App: http://localhost:8000"
echo "  PHPMyAdmin: http://localhost:8080"
echo ""
echo "If still getting 502 error, run:"
echo "  docker-compose logs app"
echo "  docker-compose logs mysql"
