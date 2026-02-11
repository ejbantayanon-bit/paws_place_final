-- Migration Script: Add Activity Logs and Soft Delete Support
-- Date: 2026-02-11
-- Description: Adds activity_logs table and soft delete column to menu_items

-- Add soft delete column to menu_items table


-- Create activity_logs table
CREATE TABLE activity_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  user_role ENUM('Admin', 'Cashier', 'Barista') NOT NULL,
  activity_type ENUM('LOGIN', 'LOGOUT', 'MENU_CREATE', 'MENU_UPDATE', 'MENU_DELETE', 'MENU_RESTORE', 'INVENTORY_ADJUST', 'ORDER_STATUS_CHANGE') NOT NULL,
  description TEXT NOT NULL,
  metadata JSON DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  INDEX idx_user_id (user_id),
  INDEX idx_activity_type (activity_type),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add some sample activity logs for testing
INSERT INTO activity_logs (user_id, user_role, activity_type, description, metadata) VALUES
(1, 'Admin', 'LOGIN', 'Admin logged in', '{"ip": "127.0.0.1"}'),
(2, 'Cashier', 'LOGIN', 'Cashier logged in', '{"ip": "127.0.0.1"}');
