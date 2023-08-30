<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSettings extends Model
{
    use HasFactory;

    protected $table = 'admin_settings';

    protected $fillable = [
        'admin_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}
