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

    public const PUBLIC_FIELDS = ['first_name', 'last_name'];

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_is_confirmed',
        'email_confirmation_token',
        'password_sha',
        'update_password_token',
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

    public function userPaymentCard(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customerGames(): HasMany
    {
        return $this->hasMany(CustomerGame::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'publisher_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
