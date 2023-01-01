<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GamePageDTO extends DataTransferObject
{
    private ?string $short_description;

    private ?string $long_description;

    private ?string $icon_url;

    private ?string $background_image_url;

    private ?array $description_images;

    public ?int $game_id;
}
