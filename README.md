# 💸 Expense Tracker (Laravel)

A full-featured **Expense Tracking Web Application** built with **Laravel**, allowing users to manage personal and group-based expenses, track analytics, and share costs easily with friends or teams.

---

## 📌 Features

- ✅ User Registration & Login (Authentication)
- ✅ Dashboard overview of expenses
- ✅ Add/Edit/Delete Personal Expenses
- ✅ Create and manage Groups
- ✅ Add members to groups
- ✅ Add shared group expenses and split costs
- ✅ Expense Analytics & visual insights
- ✅ Responsive UI with Laravel Blade + Tailwind CSS
- ✅ RESTful Controllers and clean MVC architecture

---

## 🛠 Tech Stack

- **Backend:** Laravel 10 (PHP), Laravel Artisan, Eloquent ORM
- **Frontend:** Blade (HTML), CSS, Tailwind CSS, JavaScript (optional)
- **Languages Used:** PHP, HTML, CSS
- **Database:** MySQL
- **Package Manager:** Composer (PHP), NPM (JS & CSS Assets)
- **Other Tools:** Laravel Artisan, Eloquent ORM

---

## 📂 Project Structure

app/
├── Http/Controllers/ # All Laravel Controllers
├── Models/ # Eloquent Models
resources/views/ # Blade templates
routes/web.php # Web Routes
public/ # Publicly accessible assets
database/migrations/ # Database structure (if added)
.env # Environment configuration


---

## 🚀 Getting Started

### Prerequisites

- PHP 8.x
- Composer
- MySQL or MariaDB
- Node.js and NPM

### Installation Steps

```bash
git clone https://github.com/your-username/expense-tracker.git
cd expense-tracker

# Install PHP dependencies
composer install

# Install front-end dependencies
npm install
npm run dev

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure your DB in .env and run migrations
php artisan migrate

# Start the server
php artisan serve
