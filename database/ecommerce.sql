-- E-commerce Website Database Schema
-- Simple beginner-friendly database structure

CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

-- Users table for user registration and login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table for storing product information
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.jpg',
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table for storing order information
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table for storing individual items in each order
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample products for testing
INSERT INTO products (name, description, price, image, stock_quantity) VALUES
('Laptop Computer', 'High-performance laptop perfect for work and gaming', 899.99, 'laptop.jpg', 10),
('Smartphone', 'Latest smartphone with advanced features', 599.99, 'smartphone.jpg', 25),
('Wireless Headphones', 'Premium quality wireless headphones with noise cancellation', 149.99, 'headphones.jpg', 15),
('Coffee Maker', 'Automatic coffee maker for perfect coffee every morning', 79.99, 'coffee-maker.jpg', 8),
('Running Shoes', 'Comfortable running shoes for all terrains', 120.00, 'shoes.jpg', 20),
('Backpack', 'Durable and stylish backpack for daily use', 49.99, 'backpack.jpg', 12);