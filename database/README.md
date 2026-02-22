# Database Setup Instructions

## Quick Setup for Any Device

### Method 1: Using phpMyAdmin (Recommended for Beginners)

1. Start XAMPP/WAMP/MAMP and ensure MySQL and Apache are running
2. Open browser and go to: `http://localhost/phpmyadmin`
3. Click on "Import" tab
4. Click "Choose File" and select `onlineshop.sql`
5. Click "Go" button at the bottom
6. Wait for import to complete
7. You should see "Import has been successfully finished" message

### Method 2: Using MySQL Command Line

```bash
# For XAMPP (Windows)
cd C:\xampp\mysql\bin
mysql -u root -p < path/to/onlineshop.sql

# For XAMPP (Mac/Linux)
/Applications/XAMPP/xamppfiles/bin/mysql -u root < /path/to/onlineshop.sql

# For WAMP
cd C:\wamp64\bin\mysql\mysqlX.X.XX\bin
mysql -u root -p < path/to/onlineshop.sql

# For MAMP
/Applications/MAMP/Library/bin/mysql -u root -p < /path/to/onlineshop.sql
```

### Method 3: Direct Import

```bash
# Navigate to database folder
cd database

# Import the SQL file
mysql -u root -p onlineshop < onlineshop.sql
```

## Database Configuration

After importing the database, ensure your configuration files are correct:

### Update `db.php`:

```php
$servername = "localhost";
$username = "root";
$password = "";  // Change if you set a password
$db = "onlineshop";
```

### Update `config.php`:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  // Change if you set a password
define('DB_DATABASE', 'onlineshop');
```

### Update `admin/includes/db.php` (if exists)

Ensure the same settings are applied

## Default Credentials

### Admin Panel (http://localhost/cp/admin/)

- **Email:** admin@gmail.com
- **Password:** admin123

### Test User Accounts

1. **Test User**
   - Email: testuser@gmail.com
   - Password: password
2. **Demo User**
   - Email: demouser@gmail.com
   - Password: password
3. **Other User**
   - Email: otheruser@gmail.com
   - Password: password

_Note: All user passwords are "password" for testing purposes. Admin password is "admin123"._

## Database Structure

The database includes the following tables:

- `admin_info` - Admin user accounts
- `brands` - Product brands
- `cart` - Shopping cart items
- `categories` - Product categories
- `email_info` - Newsletter subscriptions
- `logs` - Activity logs
- `orders` - Order records
- `orders_info` - Order details
- `order_products` - Order product relationships
- `products` - Product catalog (71 products)
- `reviews` - Product reviews
- `user_info` - Customer accounts
- `user_info_backup` - User backup (via trigger)
- `wishlist` - User wishlists

## Sample Data

The database comes pre-loaded with:

- 71 products across 7 categories
- 6 brands (HP, Samsung, Apple, Motorolla, LG, Cloth Brand)
- 7 product categories (Electronics, Ladies Wears, Mens Wear, Kids Wear, Furnitures, Home Appliances, Electronics Gadgets)
- 1 admin account (admin@gmail.com)
- 3 test user accounts
- Sample orders and reviews
- Empty cart and wishlist (ready for fresh use)

## Troubleshooting

### Error: "Database exists"

The SQL file includes `DROP DATABASE IF EXISTS` command, so it will automatically clean and recreate the database.

### Error: "Access denied"

Check your MySQL username and password in the configuration files.

### Error: "Table already exists"

The SQL file includes `DROP TABLE IF EXISTS` for all tables, so this shouldn't happen. If it does, manually drop the tables or the entire database first.

### Foreign Key Errors

Make sure you import the complete SQL file. The tables are created in the correct order to handle foreign key relationships.

## Portable Setup

To move this project to another device:

1. Copy the entire project folder
2. Import `database/onlineshop.sql` on the new device
3. Update config files if needed (usually just change paths if any)
4. Ensure `product_images/` folder is copied with all images
5. Start your web server and access through `http://localhost/cp/`

## Notes

- The database uses UTF-8 encoding (utf8mb4_general_ci)
- All passwords should be hashed (MD5 currently used, consider upgrading to bcrypt)
- The database includes stored procedures and triggers
- Auto-increment values are preset to avoid ID conflicts

## Support

If you encounter any issues during setup:

1. Check that MySQL service is running
2. Verify database credentials
3. Check PHP error logs
4. Ensure proper folder permissions
5. Make sure all PHP extensions are enabled (mysqli, etc.)
