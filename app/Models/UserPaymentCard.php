<?php

namespace App\Models;

use Database\Factories\UsersFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPaymentCard extends Model
{
    use HasFactory;

    protected $table = 'user_payment_cards';

    protected $fillable = [
        'user_id',
        'number',
        'cvc',
        'exp_year',
        'exp_month',
        'address',
    ];

    protected $casts = [
        'address' => 'json',
    ];

    protected static function newFactory(): UsersFactory
    {
        return UsersFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
