<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameCategoryDTO extends DataTransferObject
{
    public ?string $name;

    public ?string $description;

    public ?int $company_id;
}
