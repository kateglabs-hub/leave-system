@echo off
REM Frontend Startup Verification Script for Windows
REM Run this to verify everything is working

setlocal enabledelayedexpansion

echo.
echo ================================================
echo Frontend Startup Verification
echo ================================================
echo.

REM Test 1: Docker status
echo 1. Checking Docker containers...
docker-compose ps > nul 2>&1
if errorlevel 1 (
    echo Error: Docker Compose not responding
    pause
    exit /b 1
)
echo OK: Docker Compose is responding

REM Test 2: Container count
echo 2. Checking container status...
docker-compose ps
echo.

REM Test 3: Database
echo 3. Testing database connection...
docker-compose exec mysql mysql -u leave_user -p leave_password leave_management -e "SELECT 1;" > nul 2>&1
if errorlevel 1 (
    echo WARNING: Database not yet ready (still initializing)
) else (
    echo OK: Database connected
)
echo.

REM Test 4: API
echo 4. Testing API endpoints...
for /f "delims=" %%A in ('powershell -Command "try { (curl -s http://localhost:8000/api/diagnostics.php).Content | Select-String 'php_version' } catch { exit 1 }"') do (
    if not errorlevel 1 (
        echo OK: API responding
    ) else (
        echo ERROR: API not responding
    )
)
echo.

REM Test 5: Frontend
echo 5. Testing frontend HTML...
for /f "delims=" %%A in ('powershell -Command "try { (curl -s http://localhost:8000/).Content | Select-String 'Leave Management' } catch { exit 1 }"') do (
    if not errorlevel 1 (
        echo OK: Frontend loading
    ) else (
        echo WARNING: Frontend might not be loading
    )
)
echo.

echo ================================================
echo Verification Complete!
echo ================================================
echo.
echo Access your application:
echo   Main App:        http://localhost:8000
echo   Test Page:       http://localhost:8000/public/test.html
echo   PHPMyAdmin:      http://localhost:8080
echo.
echo Default Login:
echo   Email:           admin@company.com
echo   Password:        admin123
echo.
echo Quick Commands:
echo   docker-compose logs -f      View live logs
echo   docker-compose ps           Check status
echo   docker-compose restart      Restart services
echo   docker-compose down         Stop services
echo.
echo Tip: Open http://localhost:8000/public/test.html to run diagnostic tests
echo.
pause
