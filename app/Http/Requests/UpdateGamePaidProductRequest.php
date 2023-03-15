<?php

namespace App\Http\Requests;

use App\DTO\GameDTO;
use App\DTO\GamePageDTO;
use App\DTO\PaidProductDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGamePaidProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'price' => 'required|int',
            'currency' => 'required|string',
        ];
    }

    public function getGameDTO(): GameDTO
    {
        $data = $this->validated();

        $product = new PaidProductDTO($data);

        return new GameDTO([
            'paid_product_data' => $product,
        ]);
    }
}
