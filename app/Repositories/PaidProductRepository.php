<?php

namespace App\Repositories;

use App\DTO\PaidProductDTO;
use App\Models\GameCategory;
use App\Models\PaidProduct;
use Throwable;

class PaidProductRepository
{
    public function create(PaidProductDTO $dto, int $gameId): PaidProduct
    {
        $dto->game_id = $gameId;

        $data = array_filter($dto->toArray(), function ($item) {
            return $item !== null;
        });

        $newPrice = $dto->price;
        $data['new_price'] = $newPrice;
        $data['price'] = 0;

        return PaidProduct::create($data);
    }

    public function update(?int $id, PaidProductDTO $dto): PaidProduct
    {
        try {
            $paidProduct = PaidProduct::find($id);

            $data = array_filter($dto->toArray(), function ($item) {
                return $item !== null;
            });

            $newPrice = $dto->price;
            $data['new_price'] = $newPrice;
            $data['price'] = $paidProduct->price;

            $paidProduct->update($data);

            $paidProduct->save();
        } catch (Throwable) {
            $paidProduct = $this->create($dto, $dto->game_id);
        }

        return $paidProduct->refresh();
    }
}
