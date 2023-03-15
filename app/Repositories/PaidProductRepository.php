<?php

namespace App\Repositories;

use App\DTO\PaidProductDTO;
use App\Models\GameCategory;
use App\Models\PaidProduct;
use Throwable;

class PaidProductRepository
{
    public function create(PaidProductDTO $data, int $gameId): PaidProduct
    {
        $data->game_id = $gameId;

        return PaidProduct::create(array_filter($data->toArray()));
    }

    public function update(?int $id, PaidProductDTO $data): PaidProduct
    {
        try {
            $paidProduct = PaidProduct::find($id);
            $paidProduct->update(array_filter($data->toArray()));

            $paidProduct->save();
        } catch (Throwable) {
            $paidProduct = PaidProduct::create(array_filter($data->toArray()));
        }

        return $paidProduct->refresh();
    }
}
