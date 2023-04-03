<?php

namespace App\Http\Requests\Administration;

use App\DTO\GameCategoryDTO;
use App\DTO\GameDTO;
use App\DTO\GamePageDTO;
use App\DTO\GameReleaseDTO;
use App\DTO\GameSecurityDTO;
use App\DTO\PaidProductDTO;
use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameESIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            'es_index' => 'required|integer'
        ];
    }
}
