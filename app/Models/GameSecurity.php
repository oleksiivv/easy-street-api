<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSecurity extends Model
{
    use HasFactory;

    protected $table = 'game_securities';

    protected $fillable = [
        'has_ads',
        'ads_providers',
        'privacy_policy_url',
        'minimum_age',
        'sensitive_content',
        'game_id',
    ];

    protected $casts = [
        'ads_providers' => 'json',
        'sensitive_content' => 'json',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
