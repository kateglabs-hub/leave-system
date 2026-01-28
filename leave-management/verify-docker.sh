#!/bin/bash

# Docker Setup Verification Script
# This script checks if Docker is properly installed and configured

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "=========================================="
echo "Docker Setup Verification"
echo "=========================================="
echo -e "${NC}"

# Check Docker installation
echo -e "\n${BLUE}1. Checking Docker installation...${NC}"
if command -v docker &> /dev/null; then
    VERSION=$(docker --version)
    echo -e "${GREEN}✓ Docker installed${NC}"
    echo "  $VERSION"
else
    echo -e "${RED}✗ Docker not found${NC}"
    echo "  Download from: https://www.docker.com/products/docker-desktop"
    exit 1
fi

# Check Docker Compose installation
echo -e "\n${BLUE}2. Checking Docker Compose installation...${NC}"
if command -v docker-compose &> /dev/null; then
    VERSION=$(docker-compose --version)
    echo -e "${GREEN}✓ Docker Compose installed${NC}"
    echo "  $VERSION"
else
    echo -e "${RED}✗ Docker Compose not found${NC}"
    echo "  Usually included with Docker Desktop"
    exit 1
fi

# Check Docker daemon
echo -e "\n${BLUE}3. Checking Docker daemon...${NC}"
if docker ps &> /dev/null; then
    echo -e "${GREEN}✓ Docker daemon is running${NC}"
else
    echo -e "${RED}✗ Docker daemon is not running${NC}"
    echo "  Please start Docker Desktop"
    exit 1
fi

# Check disk space
echo -e "\n${BLUE}4. Checking available disk space...${NC}"
AVAILABLE_SPACE=$(df /var/lib/docker 2>/dev/null | awk 'NR==2 {print int($4/1024/1024)}' || df /docker 2>/dev/null | awk 'NR==2 {print int($4/1024/1024)}' || echo "unknown")
if [ "$AVAILABLE_SPACE" != "unknown" ]; then
    if [ "$AVAILABLE_SPACE" -gt 5000 ]; then
        echo -e "${GREEN}✓ Sufficient disk space available${NC}"
        echo "  Available: ${AVAILABLE_SPACE}MB"
    else
        echo -e "${YELLOW}⚠ Limited disk space${NC}"
        echo "  Available: ${AVAILABLE_SPACE}MB (5GB recommended)"
    fi
else
    echo -e "${YELLOW}⚠ Could not determine disk space${NC}"
fi

# Check Docker images
echo -e "\n${BLUE}5. Checking Docker images...${NC}"
if docker images | grep -q "php"; then
    echo -e "${GREEN}✓ PHP image available${NC}"
else
    echo -e "${YELLOW}⚠ PHP image not cached (will download on first use)${NC}"
fi

if docker images | grep -q "mysql"; then
    echo -e "${GREEN}✓ MySQL image available${NC}"
else
    echo -e "${YELLOW}⚠ MySQL image not cached (will download on first use)${NC}"
fi

# Check .env file
echo -e "\n${BLUE}6. Checking environment configuration...${NC}"
if [ -f ".env" ]; then
    echo -e "${GREEN}✓ .env file exists${NC}"
elif [ -f ".env.docker" ]; then
    echo -e "${YELLOW}⚠ .env file not found${NC}"
    echo "  Run: cp .env.docker .env"
else
    echo -e "${RED}✗ Neither .env nor .env.docker found${NC}"
fi

# Check Docker Compose file
echo -e "\n${BLUE}7. Checking docker-compose.yml...${NC}"
if [ -f "docker-compose.yml" ]; then
    echo -e "${GREEN}✓ docker-compose.yml found${NC}"
    docker-compose config > /dev/null 2>&1 && echo -e "${GREEN}✓ Configuration is valid${NC}" || echo -e "${RED}✗ Invalid configuration${NC}"
else
    echo -e "${RED}✗ docker-compose.yml not found${NC}"
fi

# Summary
echo -e "\n${BLUE}=========================================="
echo "Verification Summary"
echo "==========================================${NC}"
echo ""
echo -e "${GREEN}✓ Your system is ready for Docker!${NC}"
echo ""
echo "Next steps:"
echo "  1. cp .env.docker .env"
echo "  2. docker-compose up -d"
echo "  3. Open http://localhost:8000"
echo ""
echo "Or use the automated script:"
echo "  ./docker-start.sh"
echo ""
