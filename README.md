<div align="center">

# 🛒 ShopSphere — E-Commerce Platform

**A complete, full-featured online shopping system built with PHP & MySQL**

[![PHP](https://img.shields.io/badge/PHP-7.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-Frontend-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Apache](https://img.shields.io/badge/Apache-Server-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

[![GitHub stars](https://img.shields.io/github/stars/md-z-ishan/ShopSphere-E-Com-Website?style=social)](https://github.com/md-z-ishan/ShopSphere-E-Com-Website/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/md-z-ishan/ShopSphere-E-Com-Website?style=social)](https://github.com/md-z-ishan/ShopSphere-E-Com-Website/network)

</div>

---

## 📸 Preview

![ShopSphere Homepage](screenshots/homepage.png)

> *ShopSphere — A modern e-commerce experience, crafted with passion by **Md Zishan***

---

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#️-technology-stack)
- [Project Structure](#-project-structure)
- [Installation](#-installation--setup)
- [Login Credentials](#-default-login-credentials)
- [Screenshots](#-screenshots)
- [Author](#-author)

---

## ✨ Features

### 🧑‍💻 Customer Side
| Feature | Description |
|---|---|
| 🔐 Authentication | Secure Registration & Login |
| 🛍️ Product Browsing | Browse & Search product catalog by category |
| 🛒 Shopping Cart | Add/remove products, manage quantities |
| ❤️ Wishlist | Save favourite products for later |
| ⭐ Reviews | Read & write product reviews |
| 💳 Checkout | Smooth checkout with order summary |
| 📦 Order Tracking | View order history & live status |
| 📧 Newsletter | Subscribe to offers & promotions |

### 🛠️ Admin Panel
| Feature | Description |
|---|---|
| 📊 Dashboard | Sales overview & key statistics |
| 📦 Products | Add, edit, delete products with images |
| 👥 Users | Manage customer accounts & roles |
| 🧾 Orders | View & update all customer orders |
| 🏭 Suppliers | Add & manage suppliers |
| 📈 Reports | Daily sales reports & activity logs |

---

## 🛠️ Technology Stack

```
Backend    →  PHP 7.x
Database   →  MySQL
Frontend   →  HTML5, CSS3, Bootstrap 4
JavaScript →  jQuery, Vanilla JS
Server     →  Apache (XAMPP / WAMP / MAMP)
Libraries  →  Slick Carousel, SweetAlert, Font Awesome, jQuery UI
```

---

## 📦 Project Structure

```
/cp (root)
├── index.php                 # Home page
├── register.php              # User registration
├── login.php                 # User login
├── product.php               # Single product view
├── products.php              # Product listing
├── cart.php                  # Shopping cart
├── wishlist.php              # Wishlist management
├── checkout.php              # Checkout page
├── checkout_process.php      # Payment processing
├── payment_success.php       # Payment confirmation
├── order_successful.php      # Order confirmation
├── myorders.php              # Order history
├── review.php                # Product reviews
├── config.php                # Config & DB connection
├── db.php                    # Database query functions
├── header.php                # Header component
├── footer.php                # Footer component
│
├── /admin                    # Admin panel
│   ├── login.php             # Admin login
│   └── /admin                # Admin dashboard
│       ├── index.php         # Dashboard
│       ├── add_products.php  # Add products
│       ├── edit_product.php  # Edit products
│       ├── products_list.php # View all products
│       ├── manageuser.php    # User management
│       ├── orders.php        # Order management
│       ├── addsuppliers.php  # Supplier management
│       ├── activity.php      # Activity logs
│       ├── salesofday.php    # Daily sales report
│       └── profile.php       # Admin profile
│
├── /css                      # Frontend stylesheets
├── /js                       # Frontend scripts
├── /img                      # Images & graphics
├── /product_images           # Uploaded product images
├── /screenshots              # Project screenshots
├── /database                 # Database files
│   └── onlineshop.sql        # Schema & seed data
└── README.md                 # This file
```

---

## 💾 Database Setup

The project uses a MySQL database named `onlineshop` with tables for:
`Users` · `Products` · `Orders` · `Order Items` · `Cart` · `Wishlist` · `Reviews` · `Suppliers` · `Activity Logs`

---

## 🚀 Installation & Setup

### Prerequisites
- XAMPP / WAMP / MAMP installed
- Apache & MySQL services running
- PHP 7.x or higher

### Step-by-Step

**1. Clone the Repository**
```bash
# Mac / Linux
cd /Applications/XAMPP/xamppfiles/htdocs/
git clone https://github.com/md-z-ishan/ShopSphere-E-Com-Website.git cp

# Windows
cd C:\xampp\htdocs\
git clone https://github.com/md-z-ishan/ShopSphere-E-Com-Website.git cp
```

**2. Import the Database**

Option A — via phpMyAdmin:
- Open: `http://localhost/phpmyadmin`
- Click **Import** → select `database/onlineshop.sql` → click **Go**

Option B — via Command Line:
```bash
mysql -u root < database/onlineshop.sql
```

**3. Configure DB Connection (if needed)**

If your MySQL has a password, update:
- `db.php`
- `config.php`

**4. Open in Browser**
```
http://localhost/cp/
```

---

## 🔑 Default Login Credentials

### 👤 Customer
| Email | Password |
|---|---|
| testuser@gmail.com | password |
| demouser@gmail.com | password |

> Or register a new account at `http://localhost/cp/register.php`

### 🔐 Admin
| Field | Value |
|---|---|
| URL | `http://localhost/cp/admin/login.php` |
| Email | `admin@gmail.com` |
| Password | `admin123` |

---

## 🔒 Security Features

- ✅ Password Hashing
- ✅ SQL Injection Prevention (Prepared Statements)
- ✅ Secure Session Management
- ✅ Input Validation (Client & Server-side)
- ✅ XSS Output Escaping

---

## 📱 Responsive Design

Built with Bootstrap — works seamlessly on:
- 🖥️ Desktop
- 💻 Laptop
- 📱 Mobile
- 📟 Tablet

---

## 🐛 Future Improvements

- [ ] Advanced search & filtering
- [ ] Product recommendations (AI-based)
- [ ] Email & SMS order notifications
- [ ] Multiple payment gateway support
- [ ] Advanced analytics dashboard
- [ ] Customer support live chat

---

## 📄 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

<div align="center">

### Md Zishan

*Full Stack Developer | PHP · MySQL · JavaScript*

[![Email](https://img.shields.io/badge/Email-mdzishan24680%40gmail.com-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:mdzishan24680@gmail.com)
[![GitHub](https://img.shields.io/badge/GitHub-md--z--ishan-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/md-z-ishan)

> *"ShopSphere was designed and developed with dedication by **Md Zishan** as a complete, production-ready e-commerce solution."*

</div>

---

## ⭐ Support

If you found this project useful:

- ⭐ **Star** this repository
- 🍴 **Fork** it to build your own version
- 🐛 **Report** any issues
- 💡 **Suggest** improvements

---

<div align="center">

**Made with ❤️ by [Md Zishan](https://github.com/md-z-ishan)**

</div>
