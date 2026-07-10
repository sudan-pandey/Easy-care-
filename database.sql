CREATE DATABASE IF NOT EXISTS business_builder;
USE business_builder;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    address VARCHAR(255),
    logo VARCHAR(255),
    slug VARCHAR(100) UNIQUE NOT NULL,
    theme VARCHAR(50) DEFAULT 'modern',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT,
    name VARCHAR(100) NOT NULL,
    price VARCHAR(50),
    description TEXT,
    image VARCHAR(255),
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);
