-- ShopSphere E-Commerce Database Schema
-- Default admin credentials: admin@shopsphere.com / Admin@123

CREATE DATABASE IF NOT EXISTS shopsphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopsphere;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL,
  `email`      VARCHAR(150)    NOT NULL,
  `password`   VARCHAR(255)    NOT NULL,
  `role`       ENUM('user','admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: categories
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL,
  `slug`       VARCHAR(110)    NOT NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: products
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED     NOT NULL,
  `name`        VARCHAR(200)     NOT NULL,
  `slug`        VARCHAR(210)     NOT NULL,
  `description` TEXT,
  `price`       DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
  `stock`       INT              NOT NULL DEFAULT 0,
  `image`       VARCHAR(500)     DEFAULT NULL,
  `featured`    TINYINT(1)       NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`),
  KEY `fk_product_category` (`category_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: cart
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity`   INT          NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_product` (`user_id`, `product_id`),
  KEY `fk_cart_user`    (`user_id`),
  KEY `fk_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_user`    FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: orders
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED  NOT NULL,
  `total`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status`     ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `name`       VARCHAR(100)  NOT NULL,
  `email`      VARCHAR(150)  NOT NULL,
  `address`    VARCHAR(255)  NOT NULL,
  `city`       VARCHAR(100)  NOT NULL,
  `state`      VARCHAR(100)  NOT NULL,
  `zip`        VARCHAR(20)   NOT NULL,
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_user` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: order_items
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `order_id`   INT UNSIGNED  NOT NULL,
  `product_id` INT UNSIGNED  NOT NULL,
  `quantity`   INT           NOT NULL DEFAULT 1,
  `price`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `fk_item_order`   (`order_id`),
  KEY `fk_item_product` (`product_id`),
  CONSTRAINT `fk_item_order`   FOREIGN KEY (`order_id`)   REFERENCES `orders`   (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- Sample Data
-- ========================================================

-- Admin user  (password: Admin@123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@shopsphere.com', '$2y$10$yyDK7IZbr.Y4efOqaZcIR.09ldB2BFmXYBvLWtQz5YmBApu5SzELC', 'admin');

-- Categories
INSERT INTO `categories` (`name`, `slug`) VALUES
('Electronics', 'electronics'),
('Clothing',    'clothing'),
('Books',       'books');

-- Products (2 per category)
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `price`, `stock`, `image`, `featured`) VALUES
(1, 'Wireless Headphones',  'wireless-headphones',  'Premium noise-cancelling wireless headphones with 30-hour battery life and deep bass.',         79.99, 50, NULL, 1),
(1, 'Smart Watch',          'smart-watch',          'Feature-rich smartwatch with heart-rate monitor, GPS, and 7-day battery life.',                 149.99, 30, NULL, 1),
(2, 'Classic Denim Jacket', 'classic-denim-jacket', 'Timeless denim jacket with a comfortable slim fit, perfect for all seasons.',                   59.99, 80, NULL, 1),
(2, 'Running Sneakers',     'running-sneakers',     'Lightweight and breathable running shoes with advanced cushioning technology.',                  89.99, 60, NULL, 0),
(3, 'Clean Code',           'clean-code',           'A handbook of agile software craftsmanship by Robert C. Martin. Essential for every developer.', 34.99, 100, NULL, 1),
(3, 'The Pragmatic Programmer', 'the-pragmatic-programmer', 'Your journey to mastery — timeless advice for software developers at any stage.',         39.99, 90, NULL, 0);
