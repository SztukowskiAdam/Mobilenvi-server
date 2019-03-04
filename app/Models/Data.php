<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    protected $table = 'data';

    protected $fillable = [
        'id',
        'station_id',
        'temperature',
        'pressure',
        'created_at',
        'updated_at',
    ];
}
