@echo off
REM Docker startup script for Leave Management System (Windows)
REM Usage: docker-start.bat

setlocal enabledelayedexpansion

cls
echo.
echo ============================================
echo Leave Management System - Docker Setup
echo ============================================
echo.

REM Check for Docker
docker --version >nul 2>&1
if errorlevel 1 (
    echo Error: Docker not found. Please install Docker Desktop for Windows.
    pause
    exit /b 1
)
echo [OK] Docker installed

REM Check for Docker Compose
docker-compose --version >nul 2>&1
if errorlevel 1 (
    echo Error: Docker Compose not found. Please install Docker Desktop.
    pause
    exit /b 1
)
echo [OK] Docker Compose installed

REM Setup environment
echo.
echo ============================================
echo Setting Up Environment
echo ============================================
echo.

if not exist .env (
    if exist .env.docker (
        copy .env.docker .env >nul
        echo [OK] Created .env from .env.docker
    ) else (
        echo Error: .env or .env.docker not found
        pause
        exit /b 1
    )
) else (
    echo [OK] .env file already exists
)

REM Build and start containers
echo.
echo ============================================
echo Building and Starting Containers
echo ============================================
echo.

docker-compose build
if errorlevel 1 (
    echo Error: Failed to build containers
    pause
    exit /b 1
)
echo [OK] Images built

docker-compose up -d
if errorlevel 1 (
    echo Error: Failed to start containers
    pause
    exit /b 1
)
echo [OK] Containers started

REM Wait for services
echo.
echo Waiting for services to initialize...
timeout /t 15 /nobreak

REM Display information
echo.
echo ============================================
echo Services Started Successfully!
echo ============================================
echo.
echo Web Application:
echo   URL: http://localhost:8000
echo.
echo PHPMyAdmin (Database Management):
echo   URL: http://localhost:8080
echo.
echo Default Login Credentials:
echo   Email: admin@company.com
echo   Password: admin123
echo.
echo Useful Docker Commands:
echo   docker-compose logs -f              - View live logs
echo   docker-compose ps                   - Show container status
echo   docker-compose exec app bash        - Access app container shell
echo   docker-compose down                 - Stop all services
echo   docker-compose down -v              - Stop and remove data
echo.
echo For more information, see DOCKER.md
echo.
pause
