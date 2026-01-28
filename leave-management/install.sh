#!/bin/bash

# Leave Management System - Installation Script
# This script automates the setup process for local development

echo "╔════════════════════════════════════════════════════════════╗"
echo "║     Leave Management System - Installation Script          ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if MySQL is installed
echo "Checking prerequisites..."
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}✗ MySQL is not installed${NC}"
    echo "Please install MySQL and try again"
    exit 1
fi
echo -e "${GREEN}✓ MySQL is installed${NC}"

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP is not installed${NC}"
    echo "Please install PHP 8.0 or higher and try again"
    exit 1
fi
echo -e "${GREEN}✓ PHP is installed${NC}"

PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1)
if [ "$PHP_VERSION" -lt 8 ]; then
    echo -e "${YELLOW}⚠ PHP version is less than 8.0${NC}"
    echo "PHP 8.0+ is recommended"
fi

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  Database Configuration"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Prompt for database credentials
read -p "Database host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Database name [leave_management]: " DB_NAME
DB_NAME=${DB_NAME:-leave_management}

read -p "Database user [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "Database password: " DB_PASSWORD
echo ""

# Test database connection
echo ""
echo "Testing database connection..."
if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" &> /dev/null; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
else
    echo -e "${RED}✗ Database connection failed${NC}"
    echo "Please check your credentials and try again"
    exit 1
fi

# Create database if it doesn't exist
echo ""
echo "Creating database..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database '$DB_NAME' created/verified${NC}"
else
    echo -e "${RED}✗ Failed to create database${NC}"
    exit 1
fi

# Import schema
echo ""
echo "Importing database schema..."
if [ -f "database/schema.sql" ]; then
    mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < database/schema.sql 2>/dev/null
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Database schema imported successfully${NC}"
    else
        echo -e "${RED}✗ Failed to import schema${NC}"
        exit 1
    fi
else
    echo -e "${RED}✗ Schema file not found${NC}"
    exit 1
fi

# Create .env file
echo ""
echo "Creating environment configuration..."
cat > .env << EOF
# Database Configuration
DB_HOST=$DB_HOST
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASSWORD=$DB_PASSWORD

# Application Settings
APP_ENV=development
APP_DEBUG=true

# Session Configuration
SESSION_LIFETIME=7200

# Timezone
APP_TIMEZONE=UTC
EOF

echo -e "${GREEN}✓ Environment file created${NC}"

# Update database config to use .env (optional enhancement)
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  Installation Complete!"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Your Leave Management System is ready to use!"
echo ""
echo "Next steps:"
echo "  1. Start the development server:"
echo "     ${GREEN}cd public && php -S localhost:8000${NC}"
echo ""
echo "  2. Open your browser and go to:"
echo "     ${GREEN}http://localhost:8000${NC}"
echo ""
echo "  3. Login with default credentials:"
echo "     ${YELLOW}Admin:${NC} admin@company.com / admin123"
echo "     ${YELLOW}HR:${NC}    hr@company.com / admin123"
echo ""
echo "  ${RED}⚠ Remember to change default passwords!${NC}"
echo ""
echo "For production deployment to Vercel, see DEPLOYMENT.md"
echo ""
echo "═══════════════════════════════════════════════════════════"
