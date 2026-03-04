# Paws Place POS & Ordering System

Paws Place is a modern, responsive Point of Sale (POS) and Customer Ordering system designed for cafes. It features role-based access for Customers (Kiosk), Cashiers (POS), and Administrators (Management Dashboard).

## 🚀 Quick Setup

This project is built with **PHP** and **MySQL**. It is designed to run locally using **XAMPP**.

### Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) (Apache 7.4+ & MySQL)
- Git (optional, for cloning)

### Installation Steps
1. **Move Project to htdocs**:
   Place the `paws_place_final` folder into your XAMPP installation directory:
   `C:\xampp\htdocs\paws_place_final\`

2. **Start Services**:
   Open the **XAMPP Control Panel** and start both **Apache** and **MySQL**.

3. **Database Setup**:
   - Open [phpMyAdmin](http://localhost/phpmyadmin/).
   - Create a new database named `paws_place` (or use your preferred name).
   - Select the database and click the **Import** tab.
   - Choose the latest SQL file from:
     `paws_place_final/paw_place/server/paws_place_backup_2026-02-27_03-49-22.sql` (or most recent).
   - Click **Go** to import the tables and sample data.

4. **Configuration**:
   Verify database connection settings in:
   `paws_place_final/paw_place/server/config/grubhound_config.json`

5. **First-Time Setup (Passwords)**:
   Run the password migration script to ensure all default passwords are securely hashed:
   [http://localhost/paws_place_final/paw_place/server/migrate_hash_passwords.php](http://localhost/paws_place_final/paw_place/server/migrate_hash_passwords.php)

---

## 🛠️ Usage & Access

Once the setup is complete, you can access the different modules via these URLs:

| Module | URL | Access |
|--------|-----|--------|
| **Kiosk Login** | [Link](http://localhost/paws_place_final/paw_place/client/1_login.php) | Select "Customer Kiosk" |
| **POS Terminal** | [Link](http://localhost/paws_place_final/paw_place/client/1_login.php) | Select "Staff/POS" |
| **Admin Panel** | [Link](http://localhost/paws_place_final/paw_place/client/1_login.php) | Select "Admin Dashboard" |

### Default Credentials
- **Admin**: `admin01` / `password` (after migration)
- **Cashier**: `cashier01` / `password` (after migration)

---

## 📂 Project Structure
- `paw_place/client/`: Frontend PHP pages, CSS, and JavaScript.
- `paw_place/server/`: Backend PHP logic, API endpoints, and configuration.
- `paw_place/server/api/`: JSON-based API endpoints for data operations.
- `database/`: SQL backups and migration scripts.

> [!NOTE]
> This is a PHP-based project. There is **no `npm install`** required as dependencies are handled natively or via included scripts.