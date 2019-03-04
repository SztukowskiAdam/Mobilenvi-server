<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Station extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'stations';

    protected $fillable = [
        'id',
        'name',
        'jwt',
        'position_x',
        'position_y'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
