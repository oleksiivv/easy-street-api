<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameDTO extends DataTransferObject
{
    public ?string $name;

    public ?string $genre;

    public ?string $status;

    public ?array $tags;

    public ?string $site;

    public ?int $game_category_id;

    public ?int $company_id;

    public ?GameReleaseDTO $game_release_data;

    public ?GamePageDTO $game_page_data;

    public ?GameSecurityDTO $game_security_data;

    public ?GameCategoryDTO $game_category_data;

    public ?PaidProductDTO $paid_product_data;

    public bool $approved = false;
}
