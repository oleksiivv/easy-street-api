<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameCategory extends Model
{
    use HasFactory;

    protected $table = 'game_categories';

    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
