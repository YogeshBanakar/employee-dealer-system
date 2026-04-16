# employee-dealer-system
Laravel-based Employee–Dealer management system with authentication, AJAX forms, validation, and server-side pagination.
# Employee Dealer Management System

A role-based web application built using Laravel that manages Employees and Dealers with authentication, profile completion, and user management features.

---

## 🚀 Features

- User Registration & Login (Employee / Dealer)
- Role-Based Access Control
- Dealer First Login Profile Completion (City, State, Zip Code)
- Employee Dashboard with System Statistics
- Dealer Listing with Zip Code Search
- CRUD Operations for Users
- Server-Side Pagination
- AJAX Form Submission
- Email Uniqueness Check (AJAX)
- Client-side & Server-side Validation

---

## 🛠️ Tech Stack

- PHP (Laravel Framework)
- MySQL
- Bootstrap 5
- JavaScript (AJAX)

---

## 📊 User Roles

### 👨‍💼 Employee
- View dashboard with statistics
- Manage users (CRUD)
- View and search dealers
- Edit dealer location

### 🧑‍💼 Dealer
- Login and complete profile on first login
- Update own location
- View personal dashboard

---

## ⚙️ Installation Steps

1. Clone the repository:
```bash
git clone https://github.com/YogeshBanakar/employee-dealer-system.git


2. Navigate to the project folder:
```bash
cd employee-dealer-system

3. Install dependencies:
```bash
composer install


4. Copy .env file:
```bash
cp .env.example .env

5. Configure database in .env

6. Generate app key:
```bash
php artisan key: generate

7. Run migrations:
```bash
php artisan migrate

8. Start server:
```bash
php artisan serve
