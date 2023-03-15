<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerGame extends Model
{
    use HasFactory;

    protected $table = 'customer_games';

    protected $fillable = [
        'downloaded',
        'favourite',
        'download_datetime',
        'game_id',
        'user_id',
        'os',
        'version',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
