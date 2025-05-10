
-- Create database
CREATE DATABASE IF NOT EXISTS tokoshop;
USE tokoshop;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  address TEXT,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255),
  category_id INT,
  featured TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  address TEXT NOT NULL,
  payment_method VARCHAR(50) NOT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO categories (name, description) VALUES 
('Electronics', 'Electronic devices and gadgets'),
('Fashion', 'Clothing, shoes, and accessories'),
('Home & Kitchen', 'Home appliances and kitchenware'),
('Books', 'Books, e-books, and audiobooks');

INSERT INTO products (name, description, price, stock, image, category_id, featured) VALUES 
('Smartphone X', 'Latest smartphone with amazing camera', 8999000, 50, 'smartphone.jpg', 1, 1),
('Laptop Pro', 'High-performance laptop for professionals', 14500000, 25, 'laptop.jpg', 1, 1),
('Men\'s T-Shirt', 'Comfortable cotton t-shirt', 129000, 100, 'tshirt.jpg', 2, 0),
('Coffee Maker', 'Automatic coffee machine', 899000, 30, 'coffee-maker.jpg', 3, 1),
('Novel: The Adventure', 'Bestselling novel of the year', 150000, 200, 'book.jpg', 4, 0);

-- Insert admin user (password: admin123)
INSERT INTO users (name, email, password, is_admin) VALUES 
('Admin', 'admin@tokoshop.com', '$2y$10$JKf.L5BW7QkCT9yPJ9kE/OaPiL3gkmE9wZLJHbT/eWQCX1XiULJVa', 1);
