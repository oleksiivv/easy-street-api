<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    public const STATUS_IN_REVIEW = 'in_review';

    public const STATUS_CONFIRMED = 'confirmed';

    protected $table = 'payouts';

    protected $fillable = [
        'amount',
        'user_id',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
