-- Migration: Add student_id column to orders table
-- This allows kiosk orders to be linked back to the student who placed them

ALTER TABLE orders ADD COLUMN student_id VARCHAR(50) NULL AFTER cashier_id;
ALTER TABLE orders ADD INDEX idx_student_id (student_id);
