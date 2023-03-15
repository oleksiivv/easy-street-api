<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GamePageDTO extends DataTransferObject
{
    public ?string $short_description;

    public ?string $long_description;

    public ?string $icon_url;

    public ?string $background_image_url;

    public ?array $description_images;

    public ?int $game_id;
}
