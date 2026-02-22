@echo off
REM Database Setup Script for Online Shop (Windows)
REM This script automates the database import process

echo ================================================
echo   Online Shop Database Setup Script
echo ================================================
echo.

REM Configuration
set DB_NAME=onlineshop
set DB_USER=root
set DB_PASS=
set SQL_FILE=onlineshop.sql

REM Check if SQL file exists
if not exist "%SQL_FILE%" (
    echo Error: %SQL_FILE% not found!
    echo Please run this script from the database folder.
    pause
    exit /b 1
)

REM Detect MySQL path
set MYSQL_PATH=

if exist "C:\xampp\mysql\bin\mysql.exe" (
    set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe
    echo Found XAMPP MySQL
) else if exist "C:\wamp64\bin\mysql\mysql5.7.31\bin\mysql.exe" (
    set MYSQL_PATH=C:\wamp64\bin\mysql\mysql5.7.31\bin\mysql.exe
    echo Found WAMP MySQL
) else if exist "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" (
    set MYSQL_PATH=C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe
    echo Found MySQL Server
) else (
    echo Error: MySQL not found!
    echo Please install MySQL or XAMPP/WAMP first.
    echo.
    echo Checked locations:
    echo - C:\xampp\mysql\bin\mysql.exe
    echo - C:\wamp64\bin\mysql\
    echo - C:\Program Files\MySQL\
    pause
    exit /b 1
)

echo.
echo Database Configuration:
echo   Database: %DB_NAME%
echo   User: %DB_USER%
echo   SQL File: %SQL_FILE%
echo.

REM Ask for confirmation
set /p CONFIRM="Do you want to proceed with the import? (Y/N): "
if /i not "%CONFIRM%"=="Y" (
    echo Setup cancelled.
    pause
    exit /b 0
)

echo.
echo Importing database...
echo.

REM Import the database
if "%DB_PASS%"=="" (
    "%MYSQL_PATH%" -u %DB_USER% < %SQL_FILE%
) else (
    "%MYSQL_PATH%" -u %DB_USER% -p%DB_PASS% < %SQL_FILE%
)

REM Check if import was successful
if %ERRORLEVEL% equ 0 (
    echo.
    echo ================================================
    echo   Database imported successfully!
    echo ================================================
    echo.
    echo Next steps:
    echo 1. Verify db.php and config.php settings
    echo 2. Start Apache and MySQL in XAMPP/WAMP
    echo 3. Access: http://localhost/cp/
    echo.
    echo Default Credentials:
    echo   Admin: admin@gmail.com / admin123
    echo   User: otheruser@gmail.com / support
    echo.
) else (
    echo.
    echo ================================================
    echo   Error importing database!
    echo ================================================
    echo.
    echo Troubleshooting:
    echo 1. Make sure MySQL is running in XAMPP/WAMP
    echo 2. Check database credentials
    echo 3. Try importing via phpMyAdmin instead
    echo.
)

pause
