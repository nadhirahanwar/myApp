# Laravel To-Do App with Profile Enhancements

This Laravel project extends a basic To-Do List application with enhanced user profile functionality.

---

## 🧩 Enhancements

### 🔧 Model (User.php)
- Added fields:
  - `nickname`
  - `avatar`
  - `phone`
  - `city`
- Updated `$fillable` to allow these fields to be saved

### 🎨 View (Blade Templates)
- `profile.blade.php` for user profile page
  - View-only and edit mode
  - Avatar displayed centered above the form
- `layouts/app.blade.php`
  - Avatar shown in the top-right navbar with fallback image
  - Nickname replaces default user name

### 🧠 Controller
- `ProfileController.php`
  - Handles viewing, editing, uploading avatar, updating password, and deleting account
  - Avatar stored in `public/storage/avatars`
  - Uses `$request->all()` and `$user->update()` (same style as TodoController)

---

## 📁 Modified Files

- `app/Http/Controllers/ProfileController.php`
- `app/Models/User.php`
- `resources/views/profile.blade.php`
- `resources/views/layouts/app.blade.php`
- `database/migrations/...` (users table columns)
- `public/images/default-avatar.png`

---

## 🚀 How to Run Locally

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

