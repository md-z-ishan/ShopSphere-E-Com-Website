# 🚀 QUICK SETUP CHECKLIST

Use this checklist when setting up the project on a new device.

## ✅ Pre-Setup Checklist

- [ ] XAMPP/WAMP/MAMP installed
- [ ] Apache service is running
- [ ] MySQL service is running
- [ ] Project files copied to web server directory

## ✅ Database Setup (Choose ONE method)

### Method 1: Automated Script (Recommended)

- [ ] Open terminal/command prompt
- [ ] Navigate to `database` folder
- [ ] Run `./setup.sh` (Mac/Linux) or `setup.bat` (Windows)
- [ ] Verify success message appears

### Method 2: phpMyAdmin

- [ ] Open http://localhost/phpmyadmin
- [ ] Click "Import" tab
- [ ] Select `database/onlineshop.sql` file
- [ ] Click "Go" button
- [ ] Wait for success message

### Method 3: Command Line

- [ ] Open terminal
- [ ] Navigate to `database` folder
- [ ] Run: `mysql -u root < onlineshop.sql`

## ✅ Configuration Check

- [ ] Check `db.php` has correct database credentials
- [ ] Check `config.php` has correct database credentials
- [ ] Check `admin/includes/db.php` (if it exists)
- [ ] Verify database name is `onlineshop`
- [ ] Verify MySQL username (default: `root`)
- [ ] Verify MySQL password (default: empty)

## ✅ Test Access

- [ ] Open browser
- [ ] Navigate to `http://localhost/cp/`
- [ ] Homepage loads without errors
- [ ] Can view products
- [ ] Can access login page

## ✅ Test Login Credentials

### Admin Panel (http://localhost/cp/admin/)

- [ ] Email: admin@gmail.com
- [ ] Password: admin123
- [ ] Successfully logged in

### User Account

- [ ] Email: testuser@gmail.com (or create new account)
- [ ] Password: password
- [ ] Successfully logged in

## ✅ Post-Setup Verification

- [ ] Products display correctly
- [ ] Cart functionality works
- [ ] Checkout process accessible
- [ ] Admin panel accessible
- [ ] Product images load properly
- [ ] No PHP errors showing on pages

## 📝 Default Configuration

```
Database: onlineshop
Host: localhost
Username: root
Password: (empty)
Port: 3306 (default)
```

## 🔧 Troubleshooting

### Cannot access website

- Check if Apache is running
- Verify the correct URL: http://localhost/cp/
- Check file permissions

### Database connection failed

- Verify MySQL is running
- Check database credentials in config files
- Ensure `onlineshop` database exists

### Blank page or errors

- Check PHP error log
- Enable error display in php.ini
- Check Apache error log

### Images not loading

- Verify `product_images/` folder exists
- Check file permissions
- Ensure images were copied with the project

## 📦 What's Included

- ✅ Complete database with structure and sample data
- ✅ 71 pre-loaded products
- ✅ 7 product categories
- ✅ 6 product brands
- ✅ Admin account ready to use
- ✅ 3 test user accounts
- ✅ Automated setup scripts
- ✅ Full documentation

## 🎯 Ready to Go!

Once all checkboxes are ticked, your online shopping system is ready to use!

**Customer Site:** http://localhost/cp/
**Admin Panel:** http://localhost/cp/admin/

Happy coding! 🎉
