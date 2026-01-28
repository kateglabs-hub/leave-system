@echo off
REM Docker Troubleshooting and Restart Script for Windows
REM Run this to fix the 502 error

cls
echo.
echo ================================================
echo Fixing Leave Management System Docker Setup
echo ================================================
echo.

REM Step 1: Stop and remove everything
echo Step 1: Stopping and removing containers and volumes...
docker-compose down -v
if errorlevel 1 (
    echo Error: Failed to stop containers
    pause
    exit /b 1
)

REM Step 2: Rebuild with fresh configuration
echo.
echo Step 2: Building images fresh...
docker-compose up -d --build
if errorlevel 1 (
    echo Error: Failed to build containers
    pause
    exit /b 1
)

REM Step 3: Wait for services
echo.
echo Step 3: Waiting for services to start (30 seconds)...
timeout /t 30 /nobreak

REM Step 4: Check status
echo.
echo Step 4: Checking container status...
docker-compose ps

REM Step 5: Show logs
echo.
echo Step 5: Application logs (last 30 lines)...
docker-compose logs app

echo.
echo ================================================
echo Setup complete!
echo ================================================
echo.
echo Access your application:
echo   Web App: http://localhost:8000
echo   PHPMyAdmin: http://localhost:8080
echo.
echo Default Login:
echo   Email: admin@company.com
echo   Password: admin123
echo.
echo If still getting 502 error, run:
echo   docker-compose logs app
echo   docker-compose logs mysql
echo.
pause
