<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameLink extends Model
{
    use HasFactory;

    protected $table = 'game_links';

    protected $fillable = [
        'game_id',

        'google_play',
        'aptoide',
        'amazon_app_store',
        'galaxy_app_store',

        'app_store',
        'tweak_box',
        'cydia',

        'microsoft_store',
        'steam',
        'epic_games_store'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
