-- E-commerce Database Schema
-- Run this SQL script to create the database and tables

CREATE DATABASE IF NOT EXISTS ecommerce;
USE ecommerce;

-- Users table for customer registration and login
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table for storing product information
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table for storing customer orders
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table for storing individual items in each order
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample products
INSERT INTO products (name, description, price, image, stock) VALUES
('Laptop', 'High-performance laptop perfect for work and gaming', 899.99, 'laptop.jpg', 10),
('Smartphone', 'Latest smartphone with advanced features', 599.99, 'smartphone.jpg', 25),
('Headphones', 'Wireless noise-cancelling headphones', 199.99, 'headphones.jpg', 15),
('Tablet', 'Lightweight tablet for entertainment and productivity', 349.99, 'tablet.jpg', 8),
('Smart Watch', 'Fitness tracking smartwatch', 249.99, 'smartwatch.jpg', 12);