<?php

namespace App\Http\Requests\ChatService;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user_id' => 'required|int',
            'message' => 'required|string',
        ];
    }
}
