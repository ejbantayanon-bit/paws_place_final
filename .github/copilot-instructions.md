# Copilot Instructions for Paws Place POS System

## Project Overview

**Paws Place** is a Foundation University cafe POS and inventory management system built with PHP, MySQL, and vanilla JavaScript. It powers three distinct user interfaces:
- **Customer Kiosk** (2_kiosk_ordering.php): Self-service ordering with dynamic menu
- **POS Terminal** (3_index.php): Cashier interface for staff orders and tracking
- **Admin Dashboard** (5_adminDashboard.php): Management of inventory, menu, and analytics

## Architecture

### Tech Stack
- **Backend**: PHP 7.4+ with MySQLi (prepared statements)
- **Frontend**: HTML5, CSS (modular), vanilla JavaScript (no frameworks)
- **Database**: MySQL with 12 tables (users, orders, menu_items, inventory_raw, recipes, etc.)
- **Session Management**: PHP $_SESSION (role-based: Admin, Cashier, Kiosk)

### Key Data Flows

1. **Authentication**: User selects role → `auth_login.php` verifies credentials → session set → redirect to dashboard
   - Kiosk mode: Password-only (Admin/Cashier can unlock)
   - Admin/Cashier: Username + password required
   - Role-based restrictions enforced (Cashier cannot access Admin, etc.)

2. **Ordering**: Client JS → `place_order.php` (POST JSON) → inserts order, consumes inventory via recipes/modifiers → returns order_id
   - Auto-generates pre_order_code (PRE-XXXXXX)
   - Transaction wraps order + inventory consumption
   - Modifiers also deduct from inventory_raw

3. **Inventory**: Dashboard/POS fetch stock via `get_inventory.php` → displays low-stock alerts
   - `update_inventory.php` adjusts quantity_on_hand + logs change to inventory_logs table
   - Recipes link menu_items to raw materials with consumption quantities

### Directory Structure
- `paw_place/client/` — HTML pages + CSS + JavaScript UIs
- `paw_place/server/` — Auth endpoints + API (in `api/` subdirectory)
- `database/` — schema.sql, data.sql (MySQL)

## Critical Patterns & Conventions

### Session-Based Authentication
- **All protected pages** include `auth_check.php` at top (redirects to login if not authenticated)
- **Password verification**: Uses `password_verify()` for bcrypt hashes, fallback to plaintext for legacy users
- **Session variables**: `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['username']`, `$_SESSION['full_name']`

### API Endpoints (JSON-based)
All endpoints in `server/api/` follow this pattern:
```php
header('Content-Type: application/json; charset=utf-8');
$conn->set_charset('utf8mb4');  // Always set for menu items, names
if ($_SERVER['REQUEST_METHOD'] !== 'POST') http_response_code(405);
```
- **GET endpoints**: Return `['success' => true, 'items' => [...]]`
- **POST endpoints**: Accept JSON via `json_decode(file_get_contents('php://input'), true)`
- **Error responses**: `http_response_code(400/500)` + `['success' => false, 'message' => '...']`

### Database Transactions
Used in `place_order.php` to ensure atomicity:
```php
$conn->begin_transaction();
try { /* insert order, consume inventory */ }
catch (Exception $e) { $conn->rollback(); /* error */ }
$conn->commit();
```

### Frontend Patterns
- **Alert system**: `alertUser(message, 'success'|'error'|'info')` displays toast in top-right
- **Modal dialogs**: Hidden div with id="modal-container", show via `classList.remove('hidden')`
- **Fetch + JSON**: All client-server calls use `fetch()` with JSON payloads
- **localStorage**: Stores `userRole`, `userName`, `userId` for offline access (not critical path)

### Responsive Design
- **Tailwind CSS** + custom CSS in `css/*.css` files (not separated via classes, embedded in CSS)
- **Mobile-first**: Grid layouts adapt from mobile → tablet → desktop
- **Kiosk-specific**: Scrollable category sidebar, large buttons for touch (css/kiosk.css)

## Common Development Tasks

### Adding a New Menu Item
1. Insert into `menu_items` table with `category_id` and `base_price`
2. If item uses raw materials, create `recipes` entries linking to `inventory_raw` with quantities
3. If item has modifiers, link in `modifiers` table with `applicable_category_id`
4. No code changes needed — UI fetches dynamically from `get_menu_items.php`

### Modifying Inventory Consumption
Edit `recipes` table (for base items) or `modifier_inventory_links` table (for add-ons):
- Adjust `quantity_consumed` DECIMAL value
- `place_order.php` will use new values on next order

### Adding a Role or Access Level
1. Add to `users.role` ENUM in schema
2. Update role checks in `auth_login.php` (add conditional for new role)
3. Create dashboard file (e.g., `4_newRole.php`) with `auth_check.php` include
4. Update login button selection in `1_login.php`

### Debugging Password Issues
- Users may have plaintext passwords (legacy) or bcrypt hashes
- `verify_pass()` in `auth_login.php` handles both transparently
- To force bcrypt migration: run `hash_passwords_now.php` (one-time script)

## Integration Points & External Dependencies

- **MySQL**: Local `localhost`, no credentials required (root user, no password)
- **Tailwind CSS**: CDN-loaded in login page only
- **Images**: `image/Paws place.jpeg` used as cafe background
- **No external APIs**: System is self-contained, no 3rd-party integrations

## Testing Checklist

- **Authentication**: Try login with invalid username, wrong password, correct credentials
- **Order flow**: Place order → verify inventory decreases → check order appears in Admin dashboard
- **Modifiers**: Order item with add-ons → confirm modifier inventory consumed
- **Role restrictions**: Try accessing POS as Kiosk user (should fail via auth_check.php)
- **Kiosk exit**: Verify staff password modal validates against Admin/Cashier credentials

## Performance Notes

- All queries use prepared statements (SQL injection safe)
- No complex joins — data fetched from individual tables, assembled in PHP/JS
- Inventory logs can grow large; consider archival strategy if querying 1000+ entries
- No caching layer — each API call queries live database (acceptable for cafe scale)

