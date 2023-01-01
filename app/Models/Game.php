<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Game extends Model
{
    use HasFactory;

    public const GAME_SORT_DIRECTION_ASC = 'ASC';

    public const GAME_SORT_DIRECTION_DESC = 'DESC';

    public const GENRES = ['arcade', 'hyper-casual', 'strategy', 'quiz', 'racing', 'victory', 'adventure'];

    public const STATUSES = ['draft', 'alpha_test', 'in_review', 'demo', 'active', 'update_required', 'cancelled', 'update_in_review', 'will-be-removed'];

    protected $table = 'games';

    protected $fillable = [
        'name',
        'genre',
        'tags',
        'site',
        'game_category_id',
        'company_id',
    ];

    protected $casts = [
        'casts' => 'json',
    ];

    public function gamePage(): HasOne
    {
        return $this->hasOne(GamePage::class);
    }

    public function gameRelease(): HasOne
    {
        return $this->hasOne(GameRelease::class);
    }

    public function gameSecurity(): HasOne
    {
        return $this->hasOne(GameSecurity::class);
    }

    public function paidProduct(): HasOne
    {
        return $this->hasOne(PaidProduct::class);
    }

    public function gameCategory(): BelongsTo
    {
        return $this->belongsTo(GameCategory::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function customerGames(): HasMany
    {
        return $this->hasMany(CustomerGame::class);
    }
}
