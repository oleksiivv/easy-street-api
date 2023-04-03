<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    public const ACCOUNT_PRICE = 15;

    public const FREE_TYPE_GAMES_LIMIT = 5;

    public const COMPANY_SORT_DIRECTION_ASC = 'ASC';

    public const COMPANY_SORT_DIRECTION_DESC = 'DESC';

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'description',
        'site',
        'phone_number',
        'publisher_id',
        'team_members',
        'address',
        'type_full',
    ];

    protected $casts = [
        'team_members' => 'array',
        'address' => 'array',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'company_id');
    }

    public function gameActions(): HasMany
    {
        return $this->hasMany(GameAction::class, 'user_id');
    }
}
