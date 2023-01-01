<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamePage extends Model
{
    use HasFactory;

    protected $table = 'game_pages';

    protected $fillable = [
        'short_description',
        'long_description',
        'icon_url',
        'background_image_url',
        'description_images',
        'game_id',
    ];

    protected $casts = [
        'description_images' => 'json',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
