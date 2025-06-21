Simple PHP eCommerce Site Setup
==============================

1. Database Setup (MySQL):
--------------------------
- Create a database named `ecommerce`.
- Run the following SQL to create tables:

CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES products(`id`) ON DELETE CASCADE
);

CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES orders(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES products(`id`)
);

2. File/Folder Permissions:
--------------------------
- Create an `uploads` folder in the project root and make it writable (for product images).

3. Admin Login:
---------------
- Email: admin@gmail.com
- Password: admin123

4. Access:
----------
- Admin panel: /admin_login.php
- Public shop: /index.php 









# Modern PC Store 🖥️

A clean and responsive web-based PC and laptop shop built using HTML, CSS, JavaScript, and PHP. It features a simple admin panel for product management and a user-friendly shopping experience.

## 🔧 Features

- 🔐 **Admin Login**: Secure login with email and password (admin@gmail.com / admin123)
- 📦 **Product Management**: Admin can add product name, description, category, and price
- 🛒 **Shopping Cart**: Users can add items, view cart, and place orders
- 📁 **Categories**: Products are filtered by categories related to PCs and laptops
- ✅ **Checkout**: Users enter name, email, address, and phone number to complete orders
- 📊 **Admin Dashboard**: View all orders placed by customers
- 💻 **Responsive Design**: Works well on desktop, tablet, and mobile
- ✨ **Simple & Modern UI**: Clean layout with no unnecessary files

## 🛠️ Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL (you can configure via PHPMyAdmin)

## 🚀 How to Use

1. Clone or download the repository
2. Import the SQL file into your MySQL database
3. Configure database connection in the PHP files
4. Access the site via a local server (e.g., XAMPP)
5. Login as admin to manage products

## 📬 Admin Credentials

- **Email**: `admin@gmail.com`
- **Password**: `admin123`

---

Feel free to improve or expand on this as you develop the project further. If you want, I can also help you write a `README.md` file or add screenshots. Just ask!
