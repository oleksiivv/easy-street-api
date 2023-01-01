<?php

namespace App\Models;

use Database\Factories\UsersFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    public const USER_SORT_DIRECTION_ASC = 'ASC';

    public const USER_SORT_DIRECTION_DESC = 'DESC';

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password_sha',
        'role_id',
    ];

    protected static function newFactory(): UsersFactory
    {
        return UsersFactory::new();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function customerGames(): HasMany
    {
        return $this->hasMany(CustomerGame::class);
    }
}
