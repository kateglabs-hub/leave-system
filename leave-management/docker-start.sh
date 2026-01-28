#!/bin/bash

# Docker startup script for Leave Management System
# Usage: ./docker-start.sh

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${BLUE}============================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}============================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check prerequisites
print_header "Checking Prerequisites"

if ! command -v docker &> /dev/null; then
    print_error "Docker not found. Please install Docker first."
    exit 1
fi
print_success "Docker installed"

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose not found. Please install Docker Compose first."
    exit 1
fi
print_success "Docker Compose installed"

# Setup environment
print_header "Setting Up Environment"

if [ ! -f .env ]; then
    if [ -f .env.docker ]; then
        cp .env.docker .env
        print_success "Created .env from .env.docker"
    else
        print_error ".env or .env.docker not found"
        exit 1
    fi
else
    print_success ".env file already exists"
fi

# Build and start containers
print_header "Building and Starting Containers"

docker-compose build
print_success "Images built"

docker-compose up -d
print_success "Containers started"

# Wait for services to be ready
print_header "Waiting for Services to Initialize"

echo "Waiting for MySQL to be ready..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if docker-compose exec -T mysql mysqladmin ping -h localhost &> /dev/null; then
        print_success "MySQL is ready"
        break
    fi
    attempt=$((attempt + 1))
    echo -n "."
    sleep 1
done

if [ $attempt -eq $max_attempts ]; then
    print_warning "MySQL took a while to start, but containers are running"
fi

# Display service information
print_header "Services Started Successfully!"

echo ""
echo -e "${GREEN}📱 Web Application:${NC}"
echo -e "   URL: ${BLUE}http://localhost:8000${NC}"
echo ""

echo -e "${GREEN}💾 PHPMyAdmin (Database Management):${NC}"
echo -e "   URL: ${BLUE}http://localhost:8080${NC}"
echo ""

echo -e "${GREEN}🔐 Default Login Credentials:${NC}"
echo -e "   Email: ${BLUE}admin@company.com${NC}"
echo -e "   Password: ${BLUE}admin123${NC}"
echo ""

echo -e "${GREEN}🐳 Useful Docker Commands:${NC}"
echo -e "   ${BLUE}docker-compose logs -f${NC}           - View live logs"
echo -e "   ${BLUE}docker-compose ps${NC}                 - Show container status"
echo -e "   ${BLUE}docker-compose exec app bash${NC}      - Access app container shell"
echo -e "   ${BLUE}docker-compose down${NC}               - Stop all services"
echo -e "   ${BLUE}docker-compose down -v${NC}            - Stop and remove data"
echo ""

echo -e "${YELLOW}📖 For more information, see DOCKER.md${NC}"
echo ""

print_success "Setup complete! Your application is ready to use."
