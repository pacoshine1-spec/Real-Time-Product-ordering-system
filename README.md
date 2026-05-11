# Real-Time Product Ordering System using PHP and MySQL

## Requirements
- XAMPP / WAMP / Laragon
- PHP 7.4 or higher
- MySQL / MariaDB

## Installation
1. Copy the folder `realtime_product_ordering_system` to your web server folder:
   - XAMPP: `htdocs`
   - WAMP: `www`
2. Open phpMyAdmin.
3. Import `database/ordering_system.sql`.
4. Check `config/db.php` if your MySQL username/password is different.
5. Run this in your browser:
   - `http://localhost/realtime_product_ordering_system/`

## Default Login Accounts

### Admin
Email: `admin@example.com`  
Password: `admin123`

### Staff
Email: `staff@example.com`  
Password: `staff123`

### Customer
Email: `customer@example.com`  
Password: `customer123`

## What Was Enhanced
- Professional landing page, login page, register page, product catalog, and admin catalog UI
- 12 seed products with generated product images
- Admin product add/edit/delete page with image upload
- Product category field with customer filtering and search
- Product stock and availability management
- Low-stock indicator in admin product cards
- More polished responsive cards, forms, hero banners, and navigation
- Safer login using hashed demo passwords
- Customer catalog only shows available products with stock

## Main Features
- Customer registration and login
- Customer product browsing, search, category filtering, cart, checkout, and order history
- Admin dashboard
- Admin product management with product pictures
- Admin/staff real-time order monitoring using AJAX polling
- Order status updates

## Admin Product Upload Notes
Uploaded images are saved in the `uploads/` folder. Make sure this folder is writable in your local server environment.
