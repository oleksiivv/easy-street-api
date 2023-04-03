<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameLinksDTO extends DataTransferObject
{
    public ?int $game_id;

    public ?array $links;
}
