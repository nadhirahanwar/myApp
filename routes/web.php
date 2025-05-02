<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Auth\Notifications\VerifyEmail;

// 🌐 Default Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// 🛂 Authentication Routes
Auth::routes();
Route::post('register', [RegisterController::class, 'register']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:login');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// 📋 To-Do Resource
Route::resource('/todo', TodoController::class);

// 👤 Profile Routes (Only for Authenticated Users)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔐 MFA Verification View (After Login)
Route::get('/verify-mfa', function () {
    return view('auth.verify-mfa');
})->middleware('guest')->name('mfa.verify');

// 🔐 Verify Submitted MFA Code
Route::post('/verify-mfa', function (Request $request) {
    $request->validate([
        'code' => 'required|string',
    ]);

    $userId = session('pending_mfa_user_id');
    $user = User::find($userId);

    if (!$user || $user->two_factor_code !== $request->code || now()->gt($user->two_factor_expires_at)) {
        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }

    // Clear code and login
    $user->update([
        'two_factor_code' => null,
        'two_factor_expires_at' => null,
    ]);

    session()->forget('pending_mfa_user_id');
    auth()->login($user);

    return redirect('/home');
});

// 🔁 Resend MFA Code (Optional)
Route::post('/resend-mfa', function () {
    $user = User::find(session('pending_mfa_user_id'));
    if ($user) {
        $code = rand(100000, 999999);
        $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Your new verification code is: {$code}", function ($message) use ($user) {
            $message->to($user->email)->subject('New Verification Code');
        });
    }

    return back()->with('status', 'A new code has been sent to your email.');
});

// 🔑 Email Verification Resend Route
Route::get('/email/verify/resend', function () {
    auth()->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Verification email sent!');
})->middleware('auth')->name('verification.resend');
