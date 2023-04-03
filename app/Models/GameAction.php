<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameAction extends Model
{
    use HasFactory;

    public const PERFORMED_BY_COMPANY = 'company';
    public const PERFORMED_BY_MODERATOR = 'moderator';
    public const PERFORMED_BY_ADMIN = 'admin';

    protected $table = 'game_actions';

    protected $fillable = [
        'type',
        'fields',
        'performed_by',
        'user_id',
        'game_id',
    ];

    protected $casts = [
        'fields' => 'json',
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
