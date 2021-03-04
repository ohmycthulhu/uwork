<?php

namespace App\Models\Nova;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Administrator extends User implements JWTSubject
{
    use SoftDeletes;

    protected $fillable = [
      'email', 'password'
    ];

    protected $hidden = [
      'password'
    ];

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims(): array
  {
    return [];
  }
}
