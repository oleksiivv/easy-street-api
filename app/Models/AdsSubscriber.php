<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsSubscriber extends Model
{
    use HasFactory;

    protected $table = 'ads_subscribers';

    protected $fillable = [
        'admin_id',
        'email',
    ];
}
