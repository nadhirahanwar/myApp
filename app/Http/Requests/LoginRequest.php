<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],  // Valid email format
            'password' => ['required', 'string', 'min:8'],  // Password must be at least 8 characters
        ];
    }
}
