<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaidProduct extends Model
{
    use HasFactory;

    public const DEFAULT_CURRENCY = 'USD';

    protected $table = 'paid_products';

    protected $fillable = [
        'price',
        'new_price',
        'currency',
        'game_id',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
