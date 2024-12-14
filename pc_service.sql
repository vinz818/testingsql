DROP DATABASE `pc_service`

-------


CREATE DATABASE pc_service;

USE pc_service;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id VARCHAR(50) NOT NULL UNIQUE,
    register_date DATE NOT NULL,
    renewal_date DATE NOT NULL,
    package VARCHAR(50) NOT NULL,
    days_left INT AS (DATEDIFF(renewal_date, CURDATE())),
    status VARCHAR(10) AS (IF(DATEDIFF(renewal_date, CURDATE()) > 0, 'Active', 'Expired'))
);

-- Logs table
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert initial admin user
INSERT INTO users (username, password) VALUES ('admin', MD5('admin'));
