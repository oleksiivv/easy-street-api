<?php

namespace App\Models;

use App\System\OperatingSystem;
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

    public const STATUSES = ['draft', 'alpha_test', 'in_review', 'demo', 'active', 'ready_for_review', 'update_required', 'cancelled', 'update_in_review', 'will-be-removed'];

    public const STATUSES_AVAILABLE_FOR_PUBLISHER = ['draft', 'alpha_test', 'demo', 'ready_for_review'];

    public const STATUSES_AVAILABLE_FOR_MODERATOR = ['draft', 'in_review', 'update_required', 'cancelled', 'update_in_review', 'will-be-removed'];

    protected $table = 'games';

    protected $fillable = [
        'name',
        'genre',
        'status',
        'tags',
        'site',
        'game_category_id',
        'company_id',
        'approved',
        'es_index'
    ];

    protected $casts = [
        'tags' => 'json',
    ];

    public const RELATIONS = ['gamePage', 'gameLinks', 'gameSecurity', 'gameReleases', 'paidProduct', 'gameCategory', 'publisher', 'customerGames'];

    public function gamePage(): HasOne
    {
        return $this->hasOne(GamePage::class);
    }

    public function gameReleases(): HasMany
    {
        return $this->hasMany(GameRelease::class);
    }

    public function gameSecurity(): HasOne
    {
        return $this->hasOne(GameSecurity::class);
    }

    public function paidProduct(): HasOne
    {
        return $this->hasOne(PaidProduct::class);
    }

    public function gameLinks(): HasOne
    {
        return $this->hasOne(GameLink::class);
    }

    public function gameCategory(): BelongsTo
    {
        return $this->belongsTo(GameCategory::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function customerGames(): HasMany
    {
        return $this->hasMany(CustomerGame::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function getReleaseByOs(string $os): string
    {
        return match ($os) {
            OperatingSystem::ANDROID => $this->gameReleases->last()->android_file_url,
            OperatingSystem::IOS => $this->gameReleases->last()->ios_file_url,
            OperatingSystem::WINDOWS => $this->gameReleases->last()->windows_file_url,
            OperatingSystem::OTHER => $this->gameReleases->last()->linux_file_url,
            OperatingSystem::MAC => $this->gameReleases->last()->mac_file_url,
        };
    }

    public function gameActions(): HasMany
    {
        return $this->hasMany(GameAction::class);
    }
}
