# AGENTS.md — Coffee Shop & Restaurant Management System

## PROJECT OVERVIEW

Bangun aplikasi management coffee shop / resto modern dengan fitur:

- POS Kasir
- QR / Barcode order per meja
- Inventory bahan baku
- Stok masuk dan keluar
- Realtime order kitchen/barista
- Laporan keuangan
- Pengeluaran operasional
- Dashboard admin
- Multi role user

Stack utama:

- Laravel 11
- Vue 3
- Inertia.js
- Tailwind CSS
- Filament Admin Panel
- MySQL
- Laravel Reverb
- QR Code Generator
- Thermal Printer ESC/POS

---

# CORE RULES

Agent wajib:

1. Analisa struktur project sebelum mengubah file.
2. Jangan menghapus file penting tanpa alasan.
3. Gunakan clean architecture.
4. Pisahkan business logic dari controller.
5. Gunakan migration dan foreign key.
6. Gunakan validation request.
7. Gunakan reusable Vue components.
8. Gunakan realtime untuk order.
9. Hindari hardcode.
10. Gunakan `.env` untuk konfigurasi.
11. Gunakan transaction database pada payment/order.
12. Setiap fitur wajib dapat dites.

---

# TECH STACK

## Backend

- Laravel 11
- PHP 8.3+
- MySQL
- Laravel Reverb
- Laravel Queue
- Laravel Scheduler

## Frontend

- Vue 3
- Inertia.js
- Tailwind CSS
- Axios
- Pinia

## Admin

- Filament v3

## Realtime

- Laravel Reverb
- WebSocket

---

# USER ROLE

## owner

Akses penuh.

## admin

- Produk
- Inventory
- Report
- Expense
- User management

## cashier

- POS
- Payment
- Order

## kitchen

- Lihat order realtime
- Update status order

## warehouse

- Kelola stok bahan
- Stock in/out

---

# DATABASE TABLES

## users

```txt
id
name
email
password
role
is_active
created_at
updated_at
```

---

## tables

```txt
id
code
name
qr_token
is_active
created_at
updated_at
```

---

## categories

```txt
id
name
slug
type
is_active
created_at
updated_at
```

type:

```txt
food
drink
other
```

---

## products

```txt
id
category_id
name
slug
description
image
base_price
is_active
is_stock_tracked
created_at
updated_at
```

---

## product_variants

```txt
id
product_id
name
price
sku
barcode
is_active
created_at
updated_at
```

---

## ingredients

```txt
id
name
unit
current_stock
minimum_stock
cost_per_unit
is_active
created_at
updated_at
```

unit:

```txt
gram
ml
pcs
kg
liter
```

---

## product_recipes

```txt
id
product_id
variant_id nullable
ingredient_id
qty_used
created_at
updated_at
```

---

## stock_movements

```txt
id
ingredient_id
type
qty
before_stock
after_stock
note
created_by
created_at
updated_at
```

type:

```txt
in
out
adjustment
```

---

## orders

```txt
id
order_number
table_id nullable
customer_name nullable
source
status
subtotal
discount
tax
total
payment_status
created_by nullable
created_at
updated_at
```

source:

```txt
cashier
qr_table
```

status:

```txt
pending
accepted
processing
ready
completed
cancelled
```

payment_status:

```txt
unpaid
paid
refunded
```

---

## order_items

```txt
id
order_id
product_id
variant_id nullable
product_name
variant_name nullable
qty
price
subtotal
note nullable
created_at
updated_at
```

---

## payments

```txt
id
order_id
method
amount
change_amount
status
paid_at nullable
created_by
created_at
updated_at
```

method:

```txt
cash
qris
transfer
ewallet
```

status:

```txt
pending
paid
failed
cancelled
```

---

## expenses

```txt
id
title
category
amount
note
expense_date
created_by
created_at
updated_at
```

---

## cash_sessions

```txt
id
user_id
opening_cash
closing_cash nullable
expected_cash nullable
difference nullable
opened_at
closed_at nullable
status
created_at
updated_at
```

status:

```txt
open
closed
```

---

# BACKEND STRUCTURE

```txt
app/
  Http/
    Controllers/
      Admin/
      Cashier/
      Kitchen/
      Customer/

    Middleware/
    Requests/

  Models/

  Services/
    OrderService.php
    StockService.php
    PaymentService.php
    ReportService.php

  Events/
  Listeners/
```

---

# SERVICE RESPONSIBILITY

## OrderService

Handle:

- Create order
- QR table order
- Update status
- Calculate subtotal
- Broadcast realtime event

---

## StockService

Handle:

- Stock in
- Stock out
- Stock adjustment
- Reduce stock from recipe
- Prevent negative stock

---

## PaymentService

Handle:

- Payment cash
- QRIS
- Change calculation
- Update order payment status

---

## ReportService

Handle:

- Daily sales
- Weekly sales
- Monthly sales
- Profit report
- Expense report
- Best seller products
- Low stock report

---

# API ROUTES

## Customer

```txt
GET    /api/customer/tables/{qr_token}/menu
POST   /api/customer/tables/{qr_token}/orders
GET    /api/customer/orders/{id}
```

---

## Cashier

```txt
GET    /api/cashier/products
POST   /api/cashier/orders
POST   /api/cashier/orders/{id}/pay
PATCH  /api/cashier/orders/{id}/status
GET    /api/cashier/orders
```

---

## Kitchen

```txt
GET    /api/kitchen/orders
PATCH  /api/kitchen/orders/{id}/status
```

---

## Admin

```txt
GET    /api/admin/reports/sales
GET    /api/admin/reports/profit
GET    /api/admin/reports/expenses
GET    /api/admin/stock/low

POST   /api/admin/stock/in
POST   /api/admin/stock/out
POST   /api/admin/stock/adjustment
```

---

# FRONTEND STRUCTURE

```txt
resources/js/

Pages/
  Auth/
  Admin/
  Cashier/
  Kitchen/
  Customer/

Components/
Layouts/
Stores/
Composables/
Services/
```

---

# REQUIRED PAGES

## Auth

```txt
Login.vue
```

---

## Admin

```txt
Dashboard.vue
Products/Index.vue
Inventory/Index.vue
Reports/Index.vue
Expenses/Index.vue
Tables/Index.vue
Users/Index.vue
```

---

## Cashier

```txt
POS.vue
Orders.vue
Payment.vue
```

---

## Kitchen

```txt
Display.vue
```

---

## Customer

```txt
Menu.vue
Cart.vue
Checkout.vue
Success.vue
```

---

# UI STYLE

Gunakan tema:

```txt
Brown
Coffee
Cream
Dark charcoal
Gold accent
```

UI wajib:

- Modern
- Minimalis
- Responsive
- Mobile friendly
- Tablet friendly
- Fast loading
- Clean dashboard

---

# POS REQUIREMENT

POS wajib:

- Search produk realtime
- Filter kategori
- Scan barcode
- Add to cart
- Update qty
- Discount
- Tax
- Split payment future ready
- Print receipt
- Payment modal
- Quick action button

---

# QR TABLE ORDER FLOW

Flow:

1. Customer scan QR meja.
2. Customer buka menu.
3. Customer pilih produk.
4. Customer checkout.
5. Order masuk realtime ke kitchen & cashier.
6. Customer lihat status order.

Format URL:

```txt
/table/{qr_token}
```

---

# REALTIME EVENT

Gunakan Laravel Reverb.

Event:

```txt
OrderCreated
OrderUpdated
PaymentCompleted
LowStockDetected
```

Channel:

```txt
orders
cashier
kitchen
stock
```

---

# INVENTORY LOGIC

Stock dikurangi HANYA saat:

```txt
order completed
```

Jangan kurangi stock saat:

```txt
pending
processing
```

Saat stock movement:

1. Simpan before_stock.
2. Simpan after_stock.
3. Simpan user pembuat.
4. Simpan note.

Jika stock < minimum_stock:

- Trigger low stock warning.

---

# PAYMENT RULES

VALIDATION:

- Order cancelled tidak boleh dibayar.
- Order paid tidak boleh dibayar ulang.
- Payment gagal jangan ubah order paid.
- Cashier wajib buka cash session.

---

# CASH SESSION FLOW

Cashier wajib:

1. Open session.
2. Input opening cash.
3. Transaksi berjalan.
4. Close session.
5. Hitung selisih otomatis.

---

# REPORT FEATURES

Laporan wajib:

## Sales

- Harian
- Mingguan
- Bulanan
- Custom date

## Finance

- Total sales
- Total expense
- Gross profit
- Net profit

Formula:

```txt
gross_profit = total_sales - product_cost

net_profit = gross_profit - expenses
```

## Product

- Best seller
- Slow seller

## Inventory

- Low stock
- Stock movement history

---

# FILAMENT ADMIN

Gunakan Filament untuk CRUD:

- User
- Table
- Category
- Product
- Product Variant
- Ingredient
- Recipe
- Stock Movement
- Expense
- Order
- Payment

Dashboard widget:

- Total sales today
- Active orders
- Low stock
- Best seller
- Expense this month

---

# VALIDATION RULES

Wajib validasi:

- Product inactive tidak boleh dijual.
- Table inactive tidak bisa order.
- Stock tidak boleh minus.
- QR token harus valid.
- Variant harus milik product.
- Qty minimal 1.
- Payment amount harus sesuai.

---

# SETUP COMMAND

## Create Project

```bash
composer create-project laravel/laravel coffee-pos
```

---

## Install Breeze

```bash
composer require laravel/breeze --dev

php artisan breeze:install vue
```

---

## Install Filament

```bash
composer require filament/filament

php artisan filament:install --panels
```

---

## Install Reverb

```bash
composer require laravel/reverb

php artisan reverb:install
```

---

## QR Generator

```bash
composer require simplesoftwareio/simple-qrcode
```

---

## Install Frontend

```bash
npm install

npm run dev
```

---

## Database

```bash
php artisan migrate
```

---

## Run Server

```bash
php artisan serve
```

---

# SEEDER DEFAULT

Buat seeder:

## User

```txt
Owner
Admin
Cashier
Kitchen
Warehouse
```

---

## Category

```txt
Coffee
Non Coffee
Food
Snack
```

---

## Product

```txt
Espresso
Americano
Latte
Cappuccino
Matcha
French Fries
```

---

## Ingredients

```txt
Coffee Bean
Milk
Sugar
Cup
Ice Cube
Syrup
```

---

## Tables

```txt
Table 1 sampai Table 20
```

---

# PHASE DEVELOPMENT

## PHASE 1

- Setup project
- Login
- Role middleware
- Dashboard

---

## PHASE 2

- CRUD category
- CRUD product
- CRUD variant

---

## PHASE 3

- Inventory
- Recipe
- Stock movement

---

## PHASE 4

- POS cashier
- Cart
- Payment cash

---

## PHASE 5

- QR order
- Customer menu
- Checkout

---

## PHASE 6

- Realtime kitchen display
- Realtime cashier

---

## PHASE 7

- Finance report
- Expense
- Cash session

---

## PHASE 8

- Thermal print
- Export report
- Production optimization

---

# CODE STYLE

## Backend

- Gunakan FormRequest.
- Gunakan Service Layer.
- Gunakan DB Transaction.
- Jangan business logic di controller.
- Gunakan eager loading.
- Gunakan pagination.

---

## Frontend

- Gunakan reusable component.
- Gunakan loading state.
- Gunakan toast notification.
- Gunakan modal confirmation.
- Gunakan composable untuk API.

---

# SECURITY RULES

- Gunakan CSRF protection.
- Validasi semua request.
- Sanitize input.
- Jangan expose env.
- Gunakan rate limit API.
- Protect admin route.
- Protect websocket channel.

---

# PERFORMANCE RULES

- Gunakan eager loading.
- Gunakan cache untuk dashboard.
- Gunakan queue untuk event berat.
- Gunakan lazy image loading.
- Gunakan pagination.

---

# PRODUCTION CHECKLIST

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache

npm run build
```

---

Pastikan:

```txt
APP_DEBUG=false
QUEUE_CONNECTION=database
```

---

# STORAGE

```bash
php artisan storage:link
```

---

# SYSTEMD SERVICES

Minimal:

```txt
laravel-app
laravel-queue
laravel-reverb
```

---

# AGENT OUTPUT FORMAT

Setelah selesai fitur:

```txt
1. File dibuat
2. File diubah
3. Command dijalankan
4. Cara test
5. Catatan bug/error
```

Agent jangan lanjut ke fitur besar berikutnya sebelum fitur sebelumnya berhasil dijalankan.
