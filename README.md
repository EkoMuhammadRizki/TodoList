# Personal Task Manager (To-Do List)

A simple, secure, and featured Personal Task Manager web application built with native PHP, MySQL, Bootstrap 5, and SweetAlert2.

## 🚀 Features

-   **User Authentication**: Secure Login & Register with password hashing (`password_hash`) and validation.
-   **Session Management**: Persistent sessions and "Remember Me" functionality using cookies.
-   **Task Management (CRUD)**: Create, Read, Update, and Delete tasks.
-   **Pagination**: Efficiently browse through tasks with pagination support.
-   **Profile System**: Separate `profiles` table linked to users.
-   **Responsive Design**: Built with Bootstrap 5 for mobile-friendly usage.
-   **Interactive UI**: SweetAlert2 for beautiful alerts and confirmations.
-   **Security**:
    -   PDO Prepared Statements for SQL injection prevention.
    -   XSS protection (output escaping).
    -   Password complexity enforcement (min 8 chars).
-   **Developer Friendly**:
    -   Structured code with global/local variable scope comments.
    -   Debug points (`// DEBUG: ...`) pre-placed for easy troubleshooting.

## 🛠 Tech Stack

-   **Frontend**: HTML5, Bootstrap 5, SweetAlert2.
-   **Backend**: Native PHP (PD0).
-   **Database**: MySQL.

## 📂 Folder Structure

```
project-root/
├─ public/           # Public accessible files
│  ├─ index.php      # Dashboard & Task List
│  ├─ login.php      # Login Page
│  ├─ register.php   # Registration Page
│  ├─ ...            # Other actions
│  └─ assets/        # CSS/JS
├─ src/              # Backend Logic
│  ├─ config.php     # DB Config & Constants
│  ├─ db.php         # PDO Connection
│  ├─ auth.php       # Authentication Helpers
│  ├─ functions.php  # General Helpers
│  └─ views/         # Layout Partials (header/footer)
├─ sql/              # Database Schema
│  └─ schema.sql
└─ README.md
```

## ⚙️ Installation & Setup

1.  **Clone/Download** this repository to your web server root (e.g., `xampp/htdocs/ToDoList`).
2.  **Database Setup**:
    -   Create a new MySQL database named `todo_app` (or whatever you prefer).
    -   Import `sql/schema.sql` into the database.
3.  **Configuration**:
    -   Open `src/config.php`.
    -   Update `DB_NAME`, `DB_USER`, and `DB_PASS` to match your local environment.
4.  **Run**:
    -   Open your browser and navigate to `http://localhost/ToDoList/public/`.

## 📖 Usage Guide

### Registration
-   Go to the Register page.
-   Enter a valid email and a password (min 8 characters).
-   If valid, you will be redirected to Login.

### Login
-   Enter credentials.
-   Check "Remember Me" to stay logged in across browser restarts.

### Dashboard
-   View all your tasks.
-   Use pagination links at the bottom if you have many tasks.
-   Click "Add Task" to create new items.
-   Use "Edit" or "Delete" buttons on each task card/row.

## 🐛 Debugging

The code includes commented-out debug lines to help you understand the flow.
Look for `// DEBUG:` comments in the source files.
Example in `src/db.php`:
```php
// DEBUG: var_dump($pdo);
```
Uncomment these lines to inspect variables during runtime.

---
**Created for Educational Purposes.**
