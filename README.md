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


### Code Implementation Details

#### 1. `CreateNewUser.php`

* **Validation:** Input fields (`name`, `email`, `password`) are validated using Laravel’s built-in rules.

* **Salt Generation:**

  ```php
  $salt = Str::random(16);
  ```

  * A random 16-character string is generated for each user.

* **Password Hashing:**

  ```php
  $combinedPassword = $salt . $input['password'];
  $hashedPassword = Hash::make($combinedPassword);
  ```

  * Password is concatenated with the salt before being passed to Laravel’s `Hash::make()` which uses Bcrypt.

* **User Creation:**

  ```php
  return User::create([...]);
  ```

  * Stores the salt and hashed password in the database.

#### 2. `User.php`

* The `salt` field is assumed to be stored in the `users` table.
* The `password` field is `hashed`, but this is bypassed by manually calling `Hash::make()` after salting.
* Ensures sensitive fields (`password`, `remember_token`) are hidden from JSON responses.

#### 3. `config/hashing.php`

  ```php
  Hash::make($combinedPassword)
  ```
* Bcrypt configuration is available under the `'bcrypt'` key (e.g., `'rounds' => 10`).


### Sequence of Execution

1. User submits registration form.
2. System validates all required fields.
3. A unique salt is generated and combined with the plaintext password.
4. The combined string is hashed using Bcrypt.
5. Salt and hashed password are stored in the database.

---

### 3. Rate Limiting for Login Attempts
**Files modified:**
- `app/Providers/RouteServiceProvider.php`
- `routes/web.php`


### Code Implementation Details

#### 1. `RouteServiceProvider.php`

* The method `configureRateLimiting()` defines a rate limiter named `login`:

  ```php
  RateLimiter::for('login', function (Request $request) {
      $email = (string) $request->input('email', 'guest');
      return Limit::perMinute(1)
          ->by(Str::lower($email) . '|' . $request->ip())
          ->attempts(3)
          ->response(function () {
              return response('Too many login attempts. Please try again in 60 seconds.', 429);
          });
  });
  ```

* This configuration:

  * Identifies users by their **email address and IP**.
  * Allows **3 attempts per minute**.
  * Returns an error response if exceeded.

#### 2. `web.php`

* The login route is protected with the `throttle:login` middleware:

  ```php
  Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');
  ```
* This ensures the limiter defined in `RouteServiceProvider` is enforced on login submissions.


### Sequence of Execution

1. User submits a login form.
2. System checks if the number of attempts from that email and IP is within the allowed limit.
3. If within limit:

   * Proceed with normal login logic.
4. If limit is exceeded:

   * Return a `429` response with a message indicating to wait 60 seconds.
  
---

### 4. User Registration Enhancements
**Files modified/created:**
- `app/Models/User.php`
- `app/Actions/Fortify/CreateNewUser.php`

**Enhancements made:**
- Added **salts** for passwords during user registration.
- A **random alphanumeric salt** is generated for each user and stored in the `users` table.

---

### 5. Views for MFA
**Files created/modified:**
- `resources/views/auth/verify-mfa.blade.php`

**Enhancements made:**
- Created a **view for MFA verification**, where the user see after they logim.

---

### 6. Routes for MFA and Login
**Files modified:**
- `routes/web.php`

### Route Implementation Details

#### 1. **Display MFA Verification Form**

```php
Route::get('/verify-mfa', function () {
    return view('auth.verify-mfa');
})->middleware('guest')->name('mfa.verify');
```

* Returns the `verify-mfa.blade.php` view.
* Only accessible to unauthenticated users.

#### 2. **Verify Submitted MFA Code**

```php
Route::post('/verify-mfa', function (Request $request) {
    ...
});
```

* Validates the `code` input:

  ```php
  $request->validate(['code' => 'required|string']);
  ```
* Finds the user via `pending_mfa_user_id` from the session.
* Checks for:

  * Correct code match.
  * Code expiration (`two_factor_expires_at`).
* If valid:

  * Clears the code and timestamp.
  * Logs the user in via `auth()->login($user)`.
  * Redirects to `/home`.

#### 3. **Resend MFA Code**

```php
Route::post('/resend-mfa', function () {
    ...
});
```

* Retrieves the user from the session.
* Generates a new 6-digit code.
* Updates the expiration timestamp (10 minutes).

#### 4. **Login Route Throttling**

```php
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');
```

* Adds rate limiting (configured in `RouteServiceProvider`) to prevent abuse by limiting login attempts.

---

## Screenshots
### 1. Verify MFA
![Verify](public/images/verify.png)


---


# Assignment 3
### Enhancements
#### 1. Role-Based Access Control (RBAC)

**Files modified/created:**
* `app/Models/User.php`
* `app/Models/Role.php`
* `app/Models/Permission.php`
* `database/migrations/create_roles_table.php`
* `database/migrations/create_user_roles_table.php`
* `database/migrations/create_role_permissions_table.php`

### Code Implementation Details

#### 1. `User.php`

Defines relationships:

```php
public function roles()
{
    return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
}

public function permissions()
{
    return $this->hasManyThrough(Permission::class, Role::class, 'role_id', 'permission_id');
}
```

* `roles()`: Associates a user with multiple roles via `user_roles` table.
* `permissions()`: Retrieves all permissions granted via roles, using Laravel's `hasManyThrough` relationship.


#### 2. `Role.php`

Defines relationship:

```php
public function permissions()
{
    return $this->belongsToMany(Permission::class, 'role_permissions');
}
```

* Maps each role to its respective permissions using the `role_permissions` table.

#### 3. `Permission.php`

Defines inverse relationship:

```php
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_permissions');
}
```

### Migration 

#### a) `roles` table

Stores predefined roles.

```php
$table->string('role_name');
$table->string('description')->nullable();
```

* Example entries: `Admin`, `User`, with optional descriptions.

#### b) `user_roles` table 

```php
Schema::create('user_roles', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('role_id');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
});
```

#### c) `role_permissions` table

CRUD permission relationships for each role.

```php
$table->unsignedBigInteger('role_id');
$table->unsignedBigInteger('permission_id');
```

* Foreign keys enforce referential integrity.
* Each role can be granted specific operations (example: Create, Update).


### Sequence 
1. When a user logs in, their roles are fetched via `roles()`.
2. Each role’s permissions are retrieved via `permissions()`.
3. Logic within the controllers or middleware can now determine:

   * What actions to show or hide.
   * What views or routes the user can access.

---

#### 2. Authorization Layer

**Files modified:**

* `app/Http/Middleware/CheckRole.php`
* `routes/web.php`

**Enhancements made:**

* Created a custom middleware `CheckRole` to restrict access to specific routes based on user roles and permissions.
* The middleware performs the following checks:
  * Verifies whether the authenticated user has the required role (e.g., `Admin`, `User`).
  * Optionally checks if the user has a specific permission (e.g., `Create`, `Update`, `Delete`) when specified.
* Unauthorized users are redirected to `/home` with an appropriate error message.

---
# Assignment 4
### 1. Content Security Policy (CSP)
### CSP Code Implementation

Created a middleware named `ContentSecurityPolicy` using command:

```
php artisan make:middleware ContentSecurityPolicy
```

Created a middleware file:

```
app/Http/Middleware/ContentSecurityPolicy.php
```

Middleware uses Laravel's `$response->headers->set()` method to apply the `Content-Security-Policy` header to every HTTP response:

```php
public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    $csp = "default-src 'self'; " .
           "img-src 'self' data: https://trusted-image-cdn.com; " .
           "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; " .
           "font-src 'self' https://fonts.bunny.net; " .
           "script-src 'self' 'unsafe-eval' 'unsafe-inline'; " .
           "object-src 'none';";

    $response->headers->set('Content-Security-Policy', $csp);

    return $response;
}
```

**Explanation of each directive:**

| Directive                                                  | Purpose                                                                                   |
| ---------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| `default-src 'self'`                                       | Only allow content from the same origin                                      |
| `img-src 'self' data: https://trusted-image-cdn.com`       | Allow images from the same origin, base64-encoded data, and a trusted image CDN           |
| `style-src 'self' 'unsafe-inline' https://fonts.bunny.net` | Allow styles from the same origin and the Bunny fonts CDN, including inline styles        |
| `font-src 'self' https://fonts.bunny.net`                  | Allow font files from my app and Bunny CDN                                              |
| `script-src 'self' 'unsafe-eval' 'unsafe-inline'`          | Allow scripts from the same origin, including inline and eval scripts|
| `object-src 'none'`                                        | Block use of `<object>`, `<embed>`, and similar tags completely                           |

This policy limits where the browser can load content from, reducing the risk of malicious code execution.

Apply the CSP globally to all incoming requests, the middleware is registered in the global middleware stack in `app/Http/Kernel.php`:

```php
protected $middleware = [
    \App\Http\Middleware\ContentSecurityPolicy::class,
];
```
---
### 2. XSS (Cross-Site Scripting) Defense
### XSS Code Implementation
#### 1. Output Encoding in Blade
**Code Used:**

```blade
{{ $user->nickname }}
```

**Explanation:**
All user-generated content displayed in Blade views uses the `{{ }}` syntax that will ensure the output is HTML-escaped and prevents script injection. 

---

#### 2. Input Validation with Regular Expressions

**File:**

* `app/Http/Requests/RegisterRequest.php`

**Code:**

```php
'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z]+$/'],
```

**Explanation:**
Input fields `name` are validated to only accept alphabetic characters. This prevents malicious input such as script tags or encoded XSS from being submitted through the registration form.

---

#### 3. HttpOnly Cookies

**File:**

* `config/session.php`

**Code Confirmed:**

```php
'http_only' => true,
```

**Explanation:**
Laravel is configured to use `HttpOnly` cookies, which prevent JavaScript from accessing session cookies via `document.cookie`. This reduces the risk of cookie theft via XSS.

---

### 3. CSRF Protection Implementation
#### 1. `VerifyCsrfToken` Middleware

**File:** `app/Http/Kernel.php`
Middleware `\App\Http\Middleware\VerifyCsrfToken::class` is registered under the `web` group:

```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\VerifyCsrfToken::class,
    ],
];
```

This middleware checks every incoming `POST`, `PUT`, `PATCH`, and `DELETE` request to verify that it includes a valid CSRF token.

---

#### 2. Blade Templates – CSRF Token Injection

**Files:**

* `resources/views/profile.blade.php`
* `resources/views/todo.blade.php`
* Form-based views

All HTML forms include `@csrf` to automatically insert the CSRF token as a hidden input:

```blade
<form method="POST" action="/todo/store">
    @csrf
</form>
```

This generates:

```html
<input type="hidden" name="_token" value="{{ csrf_token() }}">
```

Laravel uses this token to validate that the form was submitted from the same site.

---

#### 3. CSRF Token for JavaScript/AJAX

**File:** `resources/views/layouts/app.blade.php`

To allow secure AJAX requests, I included the CSRF token in a meta tag:

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

JavaScript can then read this token and attach it to headers for safe POST requests.

---
