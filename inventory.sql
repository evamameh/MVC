CREATE SCHEMA IF NOT EXISTS `inventory` DEFAULT CHARACTER SET utf8;
USE `inventory`;

CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
);

CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL
);

CREATE TABLE `suppliers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `contact` VARCHAR(100)
);

CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `category_id` INT,
  `supplier_id` INT,
  `quantity` INT DEFAULT 0,
  `price` DECIMAL(10, 2) COMMENT 'Unit price (decimal amount)',
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`)
);

CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('purchase', 'sale', 'return') NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- 2 categories
INSERT INTO `categories` (`name`) VALUES
('Electronics'),
('Office Supplies');

-- 2 suppliers (one per category)
INSERT INTO `suppliers` (`name`, `contact`) VALUES
('TechSource Trading', 'techsource@example.com'),
('OfficeHub Distributors', 'officehub@example.com');

-- 2 products per category (4 total)
INSERT INTO `products` (`name`, `category_id`, `supplier_id`, `quantity`, `price`) VALUES
-- Electronics (category 1, supplier 1)
('Wireless Mouse', 1, 1, 50, 650.00),
('USB-C Hub 7-in-1', 1, 1, 30, 1990.00),
-- Office Supplies (category 2, supplier 2)
('A4 Bond Paper (Ream)', 2, 2, 120, 289.00),
('Permanent Markers (Set of 12)', 2, 2, 80, 420.00);
