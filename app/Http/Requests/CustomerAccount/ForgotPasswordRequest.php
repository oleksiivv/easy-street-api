<?php

namespace App\Http\Requests\CustomerAccount;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'new_password' => 'required|confirmed|string',
        ];
    }
}
