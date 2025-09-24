# ✅ Real-Time Todo Application
A modern **real-time Todo application** built with **Laravel (Backend)** and **Vue.js (Frontend)** featuring **live notifications** using **Pusher/Laravel WebSockets**.

---

## ✨ Features
- 🔐 User Authentication (Login/Register with JWT)
- ✅ CRUD Operations for tasks
- 🔔 Real-time Notifications when users add/update/delete tasks
- 💾 Database Storage for notifications
- 📱 Responsive Design
- 🔍 Search & Filter functionality
- 📅 Due Date Support

---

## ⚙️ Tech Stack

### Backend
- **Laravel 10+** – PHP Framework
- **PostgreSQL** – Database
- **Pusher / Laravel WebSockets** – Real-time Broadcasting
- **JWT Authentication** – API Security

### Frontend
- **Vue.js 3** – JavaScript Framework
- **Tailwind CSS** – Styling
- **Laravel Echo** – Real-time Event Handling
- **Axios** – HTTP Client
- **Iconify** – Icons

---

## 📋 Prerequisites
Make sure you have installed:
- PHP **8.1+**
- Composer
- Node.js **16+**
- PostgreSQL **12+**
- Pusher account (or run **Laravel WebSockets** locally)

---

## 🚀 Installation

### 1. Clone the repository
```sh
git clone <your-repo-url>
cd <your-app>
```

### Backend Setup (Laravel)
```sh
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure PostgreSQL in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=todo_app
DB_USERNAME=your_postgres_username
DB_PASSWORD=your_postgres_password

# Configure Pusher in .env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### Install PostgreSQL PHP extension (if not already installed)
```sh
#or example: Ubuntu/Debian:
sudo apt-get install php-pgsql
or compile with: --with-pdo-pgsql

### Run migrations (PostgreSQL optimized)
php artisan migrate

### Start Laravel server
php artisan serve
```

### PostgreSQL Database Setup
```sh
-- Create database
CREATE DATABASE todo_app;

-- Create user (optional)
CREATE USER todo_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE todo_app TO todo_user;

-- Connect to database
\c todo_app;

-- Enable UUID extension (if needed)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
```

### Frontend Setup
```sh
# Install JavaScript dependencies
npm install

# Build for development
npm run dev

# Or build for production
npm run build
```

### Starting the Application

```sh
# Linux (Systemd)
sudo systemctl start postgresql

# macOS (Homebrew)
brew services start postgresql

# Windows
net start postgresql

php artisan serve
# Server runs on http://localhost:8000

npm run dev
# Frontend runs on http://localhost:3000
```

