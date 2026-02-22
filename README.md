# 🛍️ ShopSphere — E-Commerce Website

A fully-featured, responsive e-commerce web application built with **PHP 7.4+**, **MySQL**, **Bootstrap 5**, and **jQuery**. ShopSphere supports product browsing, shopping cart (guest + authenticated), checkout, order tracking, and a complete admin dashboard — all built without frameworks, using raw PHP with PDO.

---

## ✨ Features

### Customer-Facing
- 🏠 **Home page** — hero banner, featured products, category browsing, trust badges
- 🔍 **Product listing** — search, category filter, pagination (8 per page)
- 📄 **Product detail** — image, description, stock status, quantity selector, AJAX add-to-cart
- 🛒 **Shopping cart** — guest (session-based) & logged-in (DB-backed), AJAX update/remove
- 💳 **Checkout** — shipping form, order summary, stock reduction on order placement
- ✅ **Order confirmation** — itemised receipt, order status
- 📦 **My Orders** — list of past orders with status badges
- 🔐 **Auth** — registration (with validation), login, logout, session regeneration on login

### Admin Panel (`/admin/`)
- 📊 **Dashboard** — stats cards (users, products, orders, revenue) + recent orders table
- 📦 **Products** — list, add, edit, delete, toggle featured
- 🛍️ **Orders** — list all orders, inline status update, detailed order view
- 👥 **Users** — view all registered users

### Security
- PDO prepared statements for **all** queries
- `password_hash()` / `password_verify()` for credentials
- Session ID regeneration on login
- `htmlspecialchars()` output escaping throughout
- CSRF tokens on every form
- Admin-role check on every admin page

---

## 🗂️ Project Structure

```
ShopSphere/
├── config/
│   └── db.php                  # PDO connection
├── database/
│   └── shopsphere.sql          # Full schema + sample data
├── includes/
│   ├── auth.php                # Auth helpers & CSRF
│   ├── header.php              # Bootstrap navbar
│   └── footer.php              # Bootstrap footer + JS CDN
├── ajax/
│   └── cart.php                # AJAX cart handler (add/remove/update/count)
├── admin/
│   ├── includes/
│   │   ├── header.php          # Admin sidebar layout
│   │   └── footer.php
│   ├── index.php               # Dashboard
│   ├── products.php            # Product management
│   ├── add-product.php
│   ├── edit-product.php
│   ├── orders.php              # Order management
│   └── users.php               # User list
├── index.php                   # Home page
├── products.php                # Product listing
├── product-detail.php          # Single product
├── cart.php                    # Shopping cart
├── checkout.php                # Checkout
├── order-success.php           # Order confirmation
├── login.php
├── register.php
├── logout.php
└── orders.php                  # My orders (customer)
```

---

## 🛠️ Technology Stack

| Layer      | Technology                           |
|------------|--------------------------------------|
| Backend    | PHP 7.4+ (no framework)              |
| Database   | MySQL 5.7+ / MariaDB 10.3+ via PDO   |
| Frontend   | Bootstrap 5.3, Font Awesome 6, jQuery 3.7 |
| AJAX       | Vanilla jQuery `.post()` / `.getJSON()` |
| Auth       | PHP Sessions + bcrypt                |

---

## ⚙️ Setup Instructions

### Prerequisites
- **XAMPP** (or any LAMP/WAMP stack) with PHP 7.4+ and MySQL
- A web browser

### Steps

1. **Clone / copy** this repository into your web server root:
   ```bash
   # XAMPP on Windows:
   xcopy /E /I ShopSphere C:\xampp\htdocs\ShopSphere

   # XAMPP on macOS/Linux:
   cp -r ShopSphere /Applications/XAMPP/htdocs/
   # or
   cp -r ShopSphere /opt/lampp/htdocs/
   ```

2. **Start** Apache and MySQL in XAMPP Control Panel.

3. **Import the database**:
   - Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
   - Click **Import** → choose `database/shopsphere.sql` → click **Go**
   - This creates the `shopsphere` database with all tables and sample data.

4. **(Optional) Configure DB credentials** — edit `config/db.php` if your MySQL username/password differ from the defaults:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'shopsphere');
   define('DB_USER', 'root');
   define('DB_PASS', '');   // ← change if needed
   ```

5. **Access the site**:
   - Frontend: [http://localhost/ShopSphere/](http://localhost/ShopSphere/)
   - Admin panel: [http://localhost/ShopSphere/admin/](http://localhost/ShopSphere/admin/)

---

## 🔑 Default Credentials

| Role  | Email                    | Password   |
|-------|--------------------------|------------|
| Admin | admin@shopsphere.com     | Admin@123  |

> ⚠️ **Change the admin password** after first login in a production environment.

---

## 📸 Screenshots

> _Add screenshots here once the site is running._

| Page | Preview |
|------|---------|
| Home | _(screenshot)_ |
| Products | _(screenshot)_ |
| Cart | _(screenshot)_ |
| Admin Dashboard | _(screenshot)_ |

---

## 📝 Sample Data

The SQL file seeds:
- **3 categories**: Electronics, Clothing, Books
- **6 products** (2 per category): Wireless Headphones, Smart Watch, Classic Denim Jacket, Running Sneakers, Clean Code, The Pragmatic Programmer
- **1 admin user**: `admin@shopsphere.com` / `Admin@123`

---

## 🔒 Security Notes

- All SQL queries use **prepared statements** — no raw string concatenation
- Passwords are hashed with `PASSWORD_BCRYPT` (cost factor 10)
- Session IDs are regenerated on successful login to prevent session fixation
- All user-supplied output is escaped with `htmlspecialchars()`
- Every POST form includes a **CSRF token** verified server-side
- Admin routes check `$_SESSION['role'] === 'admin'` on every page load

---

## 📄 License

MIT © ShopSphere

