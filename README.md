## 🚀 Laravel User Activities App

This project allows users to view and filter user activities and recalculate ranks dynamically. It includes functionality to seed dummy data, filter by date and user, and calculate user ranks based on points.

---

### 🛠️ Setup Instructions

#### 1. Clone the Repository

```bash
git clone https://github.com/akashjoshi1999/healthify.git
cd healthify
```

#### 2. Install Dependencies

```bash
composer install
npm install && npm run dev
```

#### 3. Environment Setup

Copy `.env.example` to `.env` and configure your database:

```bash
cp .env.example .env
```

Update the following in `.env`:

```
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

#### 4. Generate Application Key

```bash
php artisan key:generate
```

#### 5. Run Migrations

```bash
php artisan migrate
```

#### 6. Seed Users (if needed)

```bash
php artisan db:seed --class=UserSeeder
```

#### 7. Serve the Application

```bash
php artisan serve
```

---

### 💡 Custom Features

#### Recalculate Activities and Ranks

Re-generates dummy activities and updates user ranks.

```bash
php artisan db:seed --class=UserActivitySeeder
```

Or use the "Recalculate" button in the UI.

---

### 📁 Artisan Commands Summary

| Purpose                      | Command                                               |
| ---------------------------- | ----------------------------------------------------- |
| Run Migrations               | `php artisan migrate`                                 |
| Seed Dummy Users             | `php artisan db:seed --class=UserSeeder`              |
| Seed User Activities         | `php artisan db:seed --class=UserActivitySeeder`      |
| Serve Application            | `php artisan serve`                                   |
| Generate App Key             | `php artisan key:generate`                            |
| Cache Routes & Config (Prod) | `php artisan route:cache && php artisan config:cache` |
