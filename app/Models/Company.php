<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;

    public const COMPANY_SORT_DIRECTION_ASC = 'ASC';

    public const COMPANY_SORT_DIRECTION_DESC = 'DESC';

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'description',
        'site',
        'phone_number',
        'publisher_id',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }
}
