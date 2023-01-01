<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class PaidProductDTO extends DataTransferObject
{
    public ?int $price;

    public ?string $currency;

    public ?int $game_id;
}
