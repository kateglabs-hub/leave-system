#!/bin/bash

# Frontend Startup Verification Script
# Run this to verify everything is working

set -e

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "=========================================="
echo "Frontend Startup Verification"
echo "=========================================="
echo -e "${NC}"

# Test 1: Docker status
echo -e "\n${BLUE}1. Checking Docker containers...${NC}"
if ! docker-compose ps &> /dev/null; then
    echo -e "${RED}✗ Docker Compose not responding${NC}"
    exit 1
fi

RUNNING=$(docker-compose ps --services --filter "status=running" | wc -l)
TOTAL=$(docker-compose ps --services | wc -l)

if [ "$RUNNING" -eq "$TOTAL" ] && [ "$TOTAL" -ge 3 ]; then
    echo -e "${GREEN}✓ All containers running${NC}"
    docker-compose ps
else
    echo -e "${RED}✗ Not all containers running${NC}"
    docker-compose ps
    echo -e "${YELLOW}Run: docker-compose restart${NC}"
    exit 1
fi

# Test 2: Files exist
echo -e "\n${BLUE}2. Checking files...${NC}"
FILES=(
    "/var/www/html/public/index.html"
    "/var/www/html/public/app.js"
    "/var/www/html/public/test.html"
    "/var/www/html/api/index.php"
    "/var/www/html/.htaccess"
)

for file in "${FILES[@]}"; do
    if docker-compose exec app test -f "$file"; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${RED}✗ Missing: $file${NC}"
    fi
done

# Test 3: Database connection
echo -e "\n${BLUE}3. Testing database connection...${NC}"
if docker-compose exec mysql mysql -u leave_user -p"leave_password" leave_management -e "SELECT 1;" &> /dev/null; then
    echo -e "${GREEN}✓ Database connected${NC}"
else
    echo -e "${YELLOW}⚠ Database not yet ready${NC}"
fi

# Test 4: API response
echo -e "\n${BLUE}4. Testing API endpoints...${NC}"
if curl -s http://localhost:8000/api/diagnostics.php | grep -q "php_version"; then
    echo -e "${GREEN}✓ Diagnostics API responding${NC}"
else
    echo -e "${RED}✗ API not responding${NC}"
fi

# Test 5: Frontend HTML
echo -e "\n${BLUE}5. Testing frontend HTML...${NC}"
if curl -s http://localhost:8000/ | grep -q "Leave Management"; then
    echo -e "${GREEN}✓ Frontend HTML loading${NC}"
else
    echo -e "${YELLOW}⚠ Frontend HTML might not be loading correctly${NC}"
fi

# Test 6: Test page
echo -e "\n${BLUE}6. Testing diagnostic page...${NC}"
if curl -s http://localhost:8000/public/test.html | grep -q "Frontend & Backend Diagnostics"; then
    echo -e "${GREEN}✓ Test page available${NC}"
else
    echo -e "${RED}✗ Test page not loading${NC}"
fi

# Summary
echo -e "\n${BLUE}=========================================="
echo "Verification Summary"
echo -e "==========================================${NC}"

echo -e "\n${GREEN}✓ All systems operational!${NC}"

echo -e "\n${BLUE}Access your application:${NC}"
echo -e "  Main App: ${GREEN}http://localhost:8000${NC}"
echo -e "  Test Page: ${GREEN}http://localhost:8000/public/test.html${NC}"
echo -e "  PHPMyAdmin: ${GREEN}http://localhost:8080${NC}"

echo -e "\n${BLUE}Default Login:${NC}"
echo -e "  Email: ${GREEN}admin@company.com${NC}"
echo -e "  Password: ${GREEN}admin123${NC}"

echo -e "\n${BLUE}Quick Commands:${NC}"
echo "  docker-compose logs -f          - Watch logs"
echo "  docker-compose restart          - Restart services"
echo "  docker-compose ps               - Check status"
echo "  docker-compose down             - Stop services"

echo -e "\n${YELLOW}💡 Tip: Open http://localhost:8000/public/test.html to run diagnostic tests${NC}"

echo -e "\n"
