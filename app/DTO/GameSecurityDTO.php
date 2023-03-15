<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameSecurityDTO extends DataTransferObject
{
    public ?bool $has_ads;

    public ?array $ads_providers;

    public ?string $privacy_policy_url;

    public ?int $minimum_age;

    public ?array $sensitive_content;

    public ?int $game_id;
}
