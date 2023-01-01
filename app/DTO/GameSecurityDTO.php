<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameSecurityDTO extends DataTransferObject
{
    private ?bool $has_ads;

    private ?array $ads_providers;

    private ?string $privacy_policy_url;

    private ?int $minimum_age;

    private ?array $sensitive_content;

    public ?int $game_id;
}
