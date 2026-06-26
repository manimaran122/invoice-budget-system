# Invoice & Budget Management System

Laravel interview assessment project for managing invoices, budgets, expenses, payments, reports, and currency exchange rates.

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Update `.env` with your MySQL database details:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_budget_system
DB_USERNAME=root
DB_PASSWORD=
```

Add the CurrencyFreaks API settings:

```env
CURRENCYFREAKS_API_KEY=your_api_key
CURRENCYFREAKS_BASE_URL=https://api.currencyfreaks.com/v2.0
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

Build frontend assets:

```bash
npm run build
```

Run the project locally:

```bash
php artisan serve --host=127.0.0.1 --port=8001
```

Open:

```text
http://127.0.0.1:8001
```

## Login

Admin account:

```text
Email: admin@example.com
Password: password
```

Normal user account:

```text
Email: user@example.com
Password: password
```

## Main Modules

- Admin dashboard
- Customers
- Suppliers
- Products & Services
- Purchase invoices with invoice items
- Sales invoices with invoice items
- Payments
- Budgets
- Expenses
- Reports & Analytics
- Currency exchange rate API

## Useful Commands

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
npm run build
```
