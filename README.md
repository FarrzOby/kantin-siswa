# Kantin Siswa POS System

A full-featured Point of Sale web application for school canteens built with Laravel 11 + Laravel Breeze.

## Features
- 🔐 Role-based access (Admin, Kasir/Cashier, Student)
- 🛒 Menu browsing, cart, and checkout
- 📱 QR Code scanner for QRIS payment
- 🖨️ Printable thermal-style transaction receipts
- 📊 Admin dashboard with sales reports
- 👤 User profile management

## Tech Stack
- **Backend**: Laravel 11
- **Auth**: Laravel Breeze (Blade stack)
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **QR Scanner**: jsQR (vanilla JS)
- **Database**: MySQL

## Setup Instructions

### 1. Install Laravel & Breeze
```bash
composer create-project laravel/laravel kantin-siswa
cd kantin-siswa
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

### 2. Copy these project files
Copy all files from this repository into your Laravel project root.

### 3. Database Setup
```bash
# Configure .env with your DB credentials
php artisan migrate
php artisan db:seed
```

### 4. Storage link
```bash
php artisan storage:link
```

### 5. Run
```bash
php artisan serve
npm run dev
```

## Default Users (after seeding)
| Role    | Email                  | Password |
|---------|------------------------|----------|
| Admin   | admin@kantinsiswa.com  | password |
| Kasir   | kasir@kantinsiswa.com  | password |
| Student | siswa@kantinsiswa.com  | password |

## Role Permissions
- **Admin**: Full access — manage menus, users, view all transactions
- **Kasir**: Process orders, scan QR, print receipts, manage order status
- **Student**: Browse menu, add to cart, checkout, view own orders

## Receipt Printing
Visit any completed order and click "Print Receipt". The browser print dialog will open with a thermal-style 80mm receipt layout.

Have fun pak anas :), pls jgn tanyain knp selalu keluar notif gagal menambahkan pas nambahin order tpi aslinya ketambah di keranjang, sm pls jgn tanyain knp klo nambah produk langsung nambah 2 ya, akw sdh malas debugging, udh ada diatas 5 prototype projectnya sblm ini...yg penting berfungsi, ya gak pak? ok tq.
