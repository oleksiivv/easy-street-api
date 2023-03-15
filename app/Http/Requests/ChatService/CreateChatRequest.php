<?php

namespace App\Http\Requests\ChatService;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatRequest extends FormRequest
{
    public function rules()
    {
        return [
            'game_id' => 'required|int|exists:games,id',
        ];
    }
}
