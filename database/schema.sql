-- PAWS PLACE POS & Inventory Database Schema (Advanced Version - 12 Tables)

CREATE DATABASE IF NOT EXISTS paws_place_db;
USE paws_place_db;

-- -----------------------------------------------------
-- 1. USERS TABLE (Staff Login)
-- -----------------------------------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Cashier', 'Barista') NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------
-- 2. CATEGORIES TABLE (Menu Filtering)
-- -----------------------------------------------------
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE, -- e.g., 'Milktea', 'Coffee - Hot Brew'
    is_active BOOLEAN DEFAULT TRUE,   -- Toggle entire section visibility
    sort_order INT DEFAULT 0          
);

-- -----------------------------------------------------
-- 3. MENU ITEMS TABLE (Products Sold - Linked to Categories)
-- -----------------------------------------------------
CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category_id INT NOT NULL, -- FK to Categories
    base_price DECIMAL(10, 2) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE, 
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- -----------------------------------------------------
-- 4. INVENTORY RAW TABLE (Stock Tracking)
-- -----------------------------------------------------
CREATE TABLE inventory_raw (
    raw_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE, 
    unit_of_measure VARCHAR(20) NOT NULL, 
    quantity_on_hand DECIMAL(10, 3) DEFAULT 0.000,
    reorder_point DECIMAL(10, 3) DEFAULT 10.000, 
    cost_per_unit DECIMAL(10, 2) DEFAULT 0.00 
);

-- -----------------------------------------------------
-- 5. RECIPES TABLE (Base Item Consumption)
-- -----------------------------------------------------
CREATE TABLE recipes (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    raw_id INT NOT NULL,
    quantity_consumed DECIMAL(10, 3) NOT NULL, 
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (raw_id) REFERENCES inventory_raw(raw_id) ON DELETE CASCADE
);

-- -----------------------------------------------------
-- 6. MODIFIERS TABLE (Add-on Definitions)
-- -----------------------------------------------------
CREATE TABLE modifiers (
    modifier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE, 
    display_type ENUM('Add', 'Option', 'Upgrade') NOT NULL, 
    price_add DECIMAL(10, 2) DEFAULT 0.00, 
    applicable_category_id INT, 
    FOREIGN KEY (applicable_category_id) REFERENCES categories(category_id)
);

-- -----------------------------------------------------
-- 7. MODIFIER INVENTORY LINKS TABLE (Add-on Recipes)
-- -----------------------------------------------------
CREATE TABLE modifier_inventory_links (
    link_id INT AUTO_INCREMENT PRIMARY KEY,
    modifier_id INT NOT NULL,
    raw_id INT NOT NULL, 
    quantity_consumed DECIMAL(10, 3) NOT NULL,
    FOREIGN KEY (modifier_id) REFERENCES modifiers(modifier_id) ON DELETE CASCADE,
    FOREIGN KEY (raw_id) REFERENCES inventory_raw(raw_id) ON DELETE CASCADE
);

-- -----------------------------------------------------
-- 8. SHIFTS TABLE (Cash Management)
-- -----------------------------------------------------
CREATE TABLE shifts (
    shift_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,              
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    starting_cash DECIMAL(10, 2) DEFAULT 0.00,
    expected_cash DECIMAL(10, 2) DEFAULT 0.00,
    actual_cash DECIMAL(10, 2) DEFAULT 0.00,   
    discrepancy DECIMAL(10, 2) DEFAULT 0.00,  
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- -----------------------------------------------------
-- 9. ORDERS TABLE (Transaction Ledger)
-- -----------------------------------------------------
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    pre_order_code VARCHAR(20) UNIQUE, 
    final_code VARCHAR(20) UNIQUE, 
    order_source ENUM('Kiosk', 'Manual_POS') NOT NULL, 
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('PENDING PAYMENT','PREPARING','READY','SERVED','CANCELLED') DEFAULT 'PENDING PAYMENT',
    cashier_id INT, 
    shift_id INT, 
    time_placed TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    time_paid TIMESTAMP NULL, 
    FOREIGN KEY (cashier_id) REFERENCES users(user_id),
    FOREIGN KEY (shift_id) REFERENCES shifts(shift_id)
);

-- -----------------------------------------------------
-- 10. PAYMENTS TABLE (Future Proofing)
-- -----------------------------------------------------
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('Cash', 'GCash', 'Maya') DEFAULT 'Cash',
    amount DECIMAL(10, 2) NOT NULL,
    reference_number VARCHAR(100), 
    payment_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- -----------------------------------------------------
-- 11. ORDER ITEMS TABLE (Details)
-- -----------------------------------------------------
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_sale DECIMAL(10, 2) NOT NULL, 
    modifiers JSON, -- Stores customer choices (e.g., {"Pearls": true})
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(item_id)
);

-- -----------------------------------------------------
-- 12. INVENTORY LOGS TABLE (Audit Trail)
-- -----------------------------------------------------
CREATE TABLE inventory_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    raw_id INT NOT NULL,
    user_id INT, 
    change_amount DECIMAL(10, 3) NOT NULL, 
    reason VARCHAR(255), 
    log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (raw_id) REFERENCES inventory_raw(raw_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);