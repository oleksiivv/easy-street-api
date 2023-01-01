<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameReleaseDTO extends DataTransferObject
{
    private ?string $version;

    private ?string $android_file_url;

    private ?string $ios_file_url;

    private ?string $windows_file_url;

    private ?string $mac_file_url;

    private ?string $linux_file_url;

    public ?string $release_date;

    public ?int $game_id;
}
