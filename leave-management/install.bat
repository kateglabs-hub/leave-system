@echo off
:: Leave Management System - Windows Installation Script
cls

echo ================================================================
echo      Leave Management System - Installation Script
echo ================================================================
echo.

:: Check for MySQL
where mysql >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] MySQL is not installed or not in PATH
    echo Please install MySQL and try again
    pause
    exit /b 1
)
echo [OK] MySQL is installed

:: Check for PHP
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH
    echo Please install PHP 8.0+ and try again
    pause
    exit /b 1
)
echo [OK] PHP is installed
echo.

echo ================================================================
echo   Database Configuration
echo ================================================================
echo.

set /p DB_HOST="Database host [localhost]: "
if "%DB_HOST%"=="" set DB_HOST=localhost

set /p DB_NAME="Database name [leave_management]: "
if "%DB_NAME%"=="" set DB_NAME=leave_management

set /p DB_USER="Database user [root]: "
if "%DB_USER%"=="" set DB_USER=root

set /p DB_PASSWORD="Database password: "

echo.
echo Testing database connection...
mysql -h %DB_HOST% -u %DB_USER% -p%DB_PASSWORD% -e "SELECT 1" >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Database connection failed
    echo Please check your credentials and try again
    pause
    exit /b 1
)
echo [OK] Database connection successful

echo.
echo Creating database...
mysql -h %DB_HOST% -u %DB_USER% -p%DB_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME%;" >nul 2>nul
echo [OK] Database created/verified

echo.
echo Importing database schema...
if exist "database\schema.sql" (
    mysql -h %DB_HOST% -u %DB_USER% -p%DB_PASSWORD% %DB_NAME% < database\schema.sql
    echo [OK] Database schema imported
) else (
    echo [ERROR] Schema file not found
    pause
    exit /b 1
)

echo.
echo Creating environment configuration...
(
echo # Database Configuration
echo DB_HOST=%DB_HOST%
echo DB_NAME=%DB_NAME%
echo DB_USER=%DB_USER%
echo DB_PASSWORD=%DB_PASSWORD%
echo.
echo # Application Settings
echo APP_ENV=development
echo APP_DEBUG=true
echo.
echo # Session Configuration
echo SESSION_LIFETIME=7200
echo.
echo # Timezone
echo APP_TIMEZONE=UTC
) > .env

echo [OK] Environment file created

echo.
echo ================================================================
echo   Installation Complete!
echo ================================================================
echo.
echo Your Leave Management System is ready to use!
echo.
echo Next steps:
echo   1. Start the development server:
echo      cd public ^&^& php -S localhost:8000
echo.
echo   2. Open your browser and go to:
echo      http://localhost:8000
echo.
echo   3. Login with default credentials:
echo      Admin: admin@company.com / admin123
echo      HR:    hr@company.com / admin123
echo.
echo   [!] Remember to change default passwords!
echo.
echo For production deployment to Vercel, see DEPLOYMENT.md
echo.
echo ================================================================
pause
