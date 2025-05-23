<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // ✅ Generate salt and hash the password with it
        $salt = Str::random(16);
        $combinedPassword = $salt . $input['password'];
        $hashedPassword = Hash::make($combinedPassword);

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'salt' => $salt,
            'password' => $hashedPassword,
        ]);
    }
}
