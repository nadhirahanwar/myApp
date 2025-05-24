# Laravel Project

# Assignment 1
## Enhancements 
### 1. Model (`User.php`)
**Files modified:**
- `app/Models/User.php`
- `database/migrations/2025_04_18_045321_add_profile_fields_to_users_table.php`

**Enhancements made:**
- Added new fields to the `users` table:
  - `nickname`
  - `avatar`
  - `phone`
  - `city`
  - `change password`
- Migration file created to update the database.

---

### 2. Views
**Files created/modified:**
- `resources/views/profile.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/login.blade.php`

**Enhancements made:**
- `profile.blade.php`:
  - View mode shows user info: nickname, email, phone, city, avatar
  - Edit mode allows updating info and uploading avatar
  - Password change field (optional)
  - Delete account button
- `layouts/app.blade.php`:
  - Displays user `nickname` in the top-right navigation
  - Shows avatar next to the nickname
- `register.blade.php` and `login.blade.php`:
  - Input validation using form request classes

---

### 3. Controller and Routing
**Files created/modified:**
- `app/Http/Controllers/ProfileController.php`
- `routes/web.php`

**Enhancements made:**
- Created `ProfileController` with methods:
  - `show`, `edit`, `update`, and `destroy`
- Routes added to `web.php` to handle profile viewing, updating, and deleting:
  - `/profile` (view)
  - `/profile/edit` (edit)
  - `/profile/update` (update)
  - `/profile/destroy` (delete)

---

### 4. Form Request Validation
**Files created:**
- `app/Http/Requests/RegisterRequest.php`
- `app/Http/Requests/LoginRequest.php`

**Enhancements made:**
- Registration and login fields validated using Laravel Form Request classes
- Name fields validated using regex to allow only A–Z and a–z characters

---
## Screenshots

### 1. Welcome Page
![Welcome](https://github.com/nadhirahanwar/myApp/raw/main/public/images/welcome.png)

### 2. Register Page
![Register](public/images/register.png)

### 3. Login Page
![Login](public/images/login.png)

### 4. Todo Page
![Todo](public/images/todo.png)

### 5. Profile Page
![Profile](public/images/profile.png)

### 6. Edit Profile Page
![EditProfile](public/images/editprofile.png)

---

# Assignment 2
### Enhancements
#### 1. Multi-Factor Authentication (MFA)
**Files modified/created:**
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Models/User.php`
- `resources/views/auth/verify-mfa.blade.php`
- `routes/web.php`
- `app/Actions/Fortify/CreateNewUser.php`

---

This enhancement adds a second layer of security to user login via email-based Multi-Factor Authentication (MFA). Upon successful login with credentials, users are required to enter a verification code sent to their email. The code expires after 10 minutes.

---

### Code Implementation Details

#### 1. `LoginController.php`

* **Input validation** is performed on email and password.
* The system retrieves the user based on email and verifies the password using:

  ```php
  Hash::check($user->salt . $request->password, $user->password)
  ```
* Upon successful credential match:

  * A 6-digit random code is generated and stored in `two_factor_code`.
  * An expiry timestamp (`two_factor_expires_at`) is set to 10 minutes.
  * The code is emailed to the user.
  * The user is temporarily logged out and their ID stored in session.
  * Redirects to `/verify-mfa` for verification.

#### 2. `User.php`

* Implements `MustVerifyEmail` to require email verification.
* Defines `two_factor_expires_at` as a date attribute.
* Uses standard Laravel `$fillable`, `$hidden`, and `$casts` properties.
* Includes role and permission relationships for future access control.

#### 3. `verify-mfa.blade.php`

* A Blade view that renders a form for users to input their MFA code.
* Includes a button to resend the verification code.
* Submits the code via `POST` to `route('mfa.verify')`.

#### 4. `web.php`

* Defines routes for:

  * `GET /verify-mfa` – displays the MFA form.
  * `POST /verify-mfa` – validates the code and logs in the user.
  * `POST /resend-mfa` – regenerates and emails a new code.
* MFA code is considered valid if it matches and has not expired:

  ```php
  $user->two_factor_code === $request->code &&
  now()->lte($user->two_factor_expires_at)
  ```

---

### Sequence of Execution

1. User submits login form.
2. If credentials are correct, the MFA code is generated and emailed.
3. User is redirected to `/verify-mfa`.
4. User inputs code.
5. If valid and not expired, they are authenticated and redirected to `/home`.
6. If invalid or expired, user may request a new code.

---

### 2. Password Hashing with Salt (Bcrypt)
**Files modified:**
- `app/Models/User.php`
- `config/hashing.php`
- `app/Actions/Fortify/CreateNewUser.php`

**Enhancements:**
- Implemented **password hashing** using **Bcrypt**.
- **Salts** are generated for each password during registration and stored in the `users` table.
- The password and salt are concatenated and then hashed to provide an extra layer of security.

---

### 3. Rate Limiting for Login Attempts
**Files modified:**
- `app/Providers/RouteServiceProvider.php`
- `routes/web.php`

**Enhancements:**
- Used **Laravel RateLimiter** to limit **login attempts to 3** within **a minute**.
- After 3 failed login attempts, the user is temporarily blocked and must wait before trying again.
  
---

### 4. User Registration Enhancements
**Files modified/created:**
- `app/Models/User.php`
- `app/Actions/Fortify/CreateNewUser.php`

**Enhancements:**
- Added **salts** for passwords during user registration.
- A **random alphanumeric salt** is generated for each user and stored in the `users` table.
- The password is concatenated with the salt before being hashed using **Bcrypt**.

---

### 5. Views for MFA
**Files created/modified:**
- `resources/views/auth/verify-mfa.blade.php`

**Enhancements:**
- Created a **view for MFA verification**, where the user see after they logim.

---

### 6. Routes for MFA and Login
**Files modified:**
- `routes/web.php`

**Enhancements:**
- Created routes for **MFA verification**, **resending MFA codes**, and **updating user credentials after MFA verification**.

## Screenshots
### 1. Verify MFA
![Verify](public/images/verify.png)



---


## Assignment 3

### Enhancements

#### 1. Role-Based Access Control (RBAC)

**Files modified/created:**

* `app/Models/User.php`
* `app/Http/Controllers/ProfileController.php`
* `app/Models/UserRole.php`
* `app/Models/RolePermission.php`
* `database/migrations/2025_05_23_070905_create_user_roles_table.php`
* `database/migrations/2025_05_23_071044_create_roles_table.php`
* `database/migrations/2025_05_23_090723_create_user_roles_permissions_table.php`
* `routes/web.php`

**Enhancements:**

* Implemented **Role-Based Access Control (RBAC)** to manage access to different pages and actions based on user roles (e.g., Admin, User).
* Created tables:

  * **`UserRoles`**: Stores the roles assigned to users (e.g., Admin, User).
  * **`RolePermissions`**: Defines the actions (CRUD) that each role can perform (Create, Retrieve, Update, Delete).

---

#### 2. Authorization Layer

**Files modified:**

* `app/Http/Middleware/AuthorizeRole.php`
* `routes/web.php`

**Enhancements:**

* Added an **authorization middleware** that checks if the user has the required role before allowing access to certain pages.
* **Redirect Logic**: Registered users are redirected to the **To-Do page**, while administrators are redirected to the **admin dashboard**.

---

#### 3. User Permissions

**Files modified/created:**

* `app/Http/Controllers/AdminController.php`
* `resources/views/admin/user-list.blade.php`

**Enhancements:**

* **Admin users** have access to **CRUD operations** on the user list, including:

  * User deletion
  * User activation/deactivation
* **User roles and permissions** ensure that normal users only have access to their **To-Do list** and cannot modify other users' tasks or data.

---
