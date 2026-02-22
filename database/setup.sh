#!/bin/bash

# Database Setup Script for Online Shop
# This script automates the database import process

echo "================================================"
echo "  Online Shop Database Setup Script"
echo "================================================"
echo ""

# Configuration
DB_NAME="onlineshop"
DB_USER="root"
DB_PASS=""
SQL_FILE="onlineshop.sql"

# Check if SQL file exists
if [ ! -f "$SQL_FILE" ]; then
    echo "Error: $SQL_FILE not found!"
    echo "Please run this script from the database folder."
    exit 1
fi

# Detect MySQL path
MYSQL_PATH=""

if [ -f "/Applications/XAMPP/xamppfiles/bin/mysql" ]; then
    MYSQL_PATH="/Applications/XAMPP/xamppfiles/bin/mysql"
    echo "✓ Found XAMPP MySQL (Mac)"
elif [ -f "/usr/bin/mysql" ]; then
    MYSQL_PATH="/usr/bin/mysql"
    echo "✓ Found system MySQL"
elif [ -f "/Applications/MAMP/Library/bin/mysql" ]; then
    MYSQL_PATH="/Applications/MAMP/Library/bin/mysql"
    echo "✓ Found MAMP MySQL"
elif command -v mysql &> /dev/null; then
    MYSQL_PATH="mysql"
    echo "✓ Found MySQL in PATH"
else
    echo "Error: MySQL not found!"
    echo "Please install MySQL or XAMPP/MAMP first."
    exit 1
fi

echo ""
echo "Database Configuration:"
echo "  Database: $DB_NAME"
echo "  User: $DB_USER"
echo "  SQL File: $SQL_FILE"
echo ""

# Ask for confirmation
read -p "Do you want to proceed with the import? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Setup cancelled."
    exit 0
fi

echo ""
echo "Importing database..."

# Import the database
if [ -z "$DB_PASS" ]; then
    "$MYSQL_PATH" -u "$DB_USER" < "$SQL_FILE"
else
    "$MYSQL_PATH" -u "$DB_USER" -p"$DB_PASS" < "$SQL_FILE"
fi

# Check if import was successful
if [ $? -eq 0 ]; then
    echo ""
    echo "================================================"
    echo "  ✓ Database imported successfully!"
    echo "================================================"
    echo ""
    echo "Next steps:"
    echo "1. Verify db.php and config.php settings"
    echo "2. Start Apache and MySQL"
    echo "3. Access: http://localhost/cp/"
    echo ""
    echo "Default Credentials:"
    echo "  Admin: admin@gmail.com / admin123"
    echo "  User: otheruser@gmail.com / support"
    echo ""
else
    echo ""
    echo "================================================"
    echo "  ✗ Error importing database!"
    echo "================================================"
    echo ""
    echo "Troubleshooting:"
    echo "1. Make sure MySQL is running"
    echo "2. Check database credentials"
    echo "3. Try importing via phpMyAdmin instead"
    echo ""
    exit 1
fi
