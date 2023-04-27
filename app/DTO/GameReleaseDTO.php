<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class GameReleaseDTO extends DataTransferObject
{
    public ?string $version;

    public ?string $android_file_url;

    public ?string $ios_file_url;

    public ?string $windows_file_url;

    public ?string $mac_file_url;

    public ?string $linux_file_url;

    public ?int $game_id;
}
