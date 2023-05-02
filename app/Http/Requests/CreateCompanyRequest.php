<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'company.name' => 'required|string|unique:companies,name'
        ];
    }
}
