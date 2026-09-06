# Vendorwaala 🛍️

A full-featured **multi-vendor e-commerce marketplace** built from scratch with core PHP and MySQL — inspired by platforms like Daraz and Amazon, where multiple independent vendors can sell products under one platform.

Built as a hands-on learning project to master PHP/MySQL fundamentals: authentication, role-based access control, relational database design, and secure CRUD operations — without relying on any framework.

## ✨ Features

### 🔐 Authentication & Roles
- Secure registration/login with password hashing (`password_hash`/`password_verify`) and prepared statements throughout
- Three roles: **Admin**, **Vendor**, **Customer** — each with a dedicated dashboard and access control via sessions
- Login/Register available as a modal popup from any page
- Vendor accounts require admin approval before going live; rejected vendors are blocked at login with a reason shown

### 🛠️ Admin Panel
- Approve/reject vendor applications (with rejection reason)
- Manage product categories
- Read-only overview of all orders across all vendors

### 🏪 Vendor Panel
- Full product CRUD (Create, Read, Update, Delete) with image upload
- Manage and update status (pending → shipped → delivered) **only for their own orders** — real marketplace-style authority, not centralized under admin

### 🛒 Customer Experience
- Marketplace-style homepage: sidebar categories, hero banner, promo tiles, product grid
- Product search and category filtering
- Product detail pages with reviews & star ratings
- Persistent, database-backed shopping cart
- Checkout with **Cash on Delivery** or a simulated **Online Payment** flow (sandbox — no real transactions or stored card data)
- Order history with per-item status tracking (since one order can span multiple vendors)

### 🎨 UI/UX
- Fully responsive (Bootstrap 5)
- Custom design system — purple/blue theme, hover animations, card lift effects, Google Fonts
- Two-tier navbar (search + account + cart) with role-based navigation

## 🧰 Tech Stack

- **Backend:** PHP (core, no framework) with `mysqli` and prepared statements
- **Database:** MySQL
- **Frontend:** HTML, CSS, Bootstrap 5, Bootstrap Icons
- **Environment:** XAMPP (Apache + MySQL)

## 📁 Project Structure

```
vendorwaala/
├── admin/          # Admin panel (vendor approval, categories, orders overview)
├── vendor/          # Vendor panel (products, own orders)
├── customer/         # Storefront (homepage, cart, checkout, orders, reviews)
├── auth/           # Login/Register/Logout logic
├── config/          # Database connection (db.example.php provided; db.php gitignored)
├── includes/         # Shared header/footer (navbar, auth modal)
├── assets/          # Custom CSS
└── uploads/          # Vendor-uploaded product images
```

## 🗄️ Database Design

Core tables: `users`, `vendors`, `categories`, `products`, `cart`, `orders`, `order_items`, `reviews` — connected with foreign keys to keep data relational and consistent (e.g. an order can contain items from multiple vendors, each tracked independently).

## 🚀 Setup

1. Clone the repo into your XAMPP `htdocs` folder
2. Create a MySQL database and import the schema (see `/database` if included, or run the table-creation SQL)
3. Copy `config/db.example.php` to `config/db.php` and fill in your local database credentials
4. Start Apache & MySQL via XAMPP
5. Visit `http://localhost/vendorwaala/customer/index.php`

## 🔮 Possible Future Improvements

- Real payment gateway integration (Stripe/JazzCash)
- Pagination on product listings
- Vendor-level sales analytics

## 👤 Author

**Muhammad Farhan** — built as a self-directed learning project to strengthen core PHP & MySQL skills.
