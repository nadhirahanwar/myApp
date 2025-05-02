<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\TwoFactorCodeMail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Step 1: Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Step 2: Find user by email
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($user->salt . $request->password, $user->password)) {
            // Step 3: Send 2FA code
            $this->sendTwoFactorCode($user);

            // Step 4: Temporarily logout and store user ID
            Auth::logout();
            session(['pending_mfa_user_id' => $user->id]);

            return redirect('/verify-mfa');
        }

        // Step 5: Handle failure
        throw ValidationException::withMessages([
            'email' => ['Invalid credentials.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function sendTwoFactorCode(User $user)
    {
        $user->two_factor_code = rand(100000, 999999);
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new TwoFactorCodeMail($user->two_factor_code));
    }
}
