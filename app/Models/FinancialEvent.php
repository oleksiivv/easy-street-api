<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialEvent extends Model
{
    use HasFactory;

    public const PARTNER_TYPE_PUBLISHER = 'publisher';
    public const PARTNER_TYPE_ES = 'es';

    protected $table = 'financial_events';

    protected $fillable = [
        'amount',
        'partner_type',
        'admin_id',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}
