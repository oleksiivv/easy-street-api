<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Administrator extends Model
{
    use HasFactory;

    protected $table = 'administrators_to_moderators_pivot';

    protected $fillable = [
        'user_id',
        'administrator_email',
        'moderators',
    ];

    protected $casts = [
        'moderators' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(AdminSettings::class);
    }
}
