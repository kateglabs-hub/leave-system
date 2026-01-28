#!/bin/bash
# Reset the database and apply correct password hash

echo "Resetting database..."
docker-compose down -v
echo "Waiting for containers to stop..."
sleep 2

echo "Starting containers..."
docker-compose up -d
echo "Waiting for MySQL to be ready..."
sleep 10

echo "Database reset complete. New schema with correct password hash has been applied."
echo "You can now login with:"
echo "  Email: admin@company.com"
echo "  Password: admin123"
