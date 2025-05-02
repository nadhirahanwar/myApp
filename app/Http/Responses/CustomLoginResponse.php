<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Mail;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        // Step 1: Generate a random 6-digit code
        $code = rand(100000, 999999);

        // Step 2: Save code & expiry time in DB
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Step 3: Send the code to the user's email
        Mail::raw("Your login verification code is: {$code}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Login Verification Code');
        });

        // Step 4: Logout temporarily and redirect to verify page
        auth()->logout();
        session(['pending_mfa_user_id' => $user->id]);

        return redirect('/verify-mfa');
    }
}
