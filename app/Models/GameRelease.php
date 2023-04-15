<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameRelease extends Model
{
    use HasFactory;

    protected $table = 'game_releases';

    protected $fillable = [
        'version',
        'android_file_url',
        'ios_file_url',
        'windows_file_url',
        'mac_file_url',
        'linux_file_url',
        'release_date',
        'game_id',

        'android_icon',
        'ios_icon',
        'windows_icon',
        'mac_icon',
        'linux_icon'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
