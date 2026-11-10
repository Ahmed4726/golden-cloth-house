<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    public const SINGLETON_ID = 1;

    protected $fillable = [
        'whatsapp_number',
    ];
}
