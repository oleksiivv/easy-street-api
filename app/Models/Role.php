<?php

namespace App\Models;

use Database\Factories\RolesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Role extends Model
{
    use HasFactory;

    public const ROLES = ['customer', 'publisher', 'publisher_team_member', 'admin', 'moderator'];

    public const ROLE_CUSTOMER = 'customer';

    public const ROLE_PUBLISHER = 'publisher';

    public const ROLE_PUBLISHER_TEAM_MEMBER = 'publisher_team_member';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MODERATOR = 'moderator';

    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'json'
    ];

    protected static function newFactory(): RolesFactory
    {
        return RolesFactory::new();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
