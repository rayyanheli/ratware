# iTechPOS - Setup & Installation Guide

Follow these steps to install the missing dependencies and run the Laravel ERP application locally.

## 1. Prerequisites
Make sure you have **PHP**, **Composer**, and **Node.js** installed on your system.

## 2. Setup Commands

```bash
# 1. Install PHP dependencies (Restores the excluded 'vendor' folder)
composer install

# 2. Install JavaScript dependencies (Restores the 'node_modules' folder)
npm install

# 3. Create your local environment configuration file
cp .env.example .env

# 4. Generate your unique application encryption key
php artisan key:generate
```

## 3. Database Setup
1. Create a blank database in your local database manager (MySQL/phpMyAdmin).
2. Open your newly created `.env` file and update your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
3. Run the database migrations to build your tables:
```bash
php artisan migrate --seed
```

## 4. Run the Application
Run both of these commands in separate terminal tabs to get your application running locally:

```bash
# Start the backend Laravel PHP server (Usually runs at http://127.0.0.1:8000)
php artisan serve

# Start the frontend assets compiler (Vite / Mix)
npm run dev
```
