<?php

namespace App\Models;

use App\Models\Payment\Card;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
  use SoftDeletes, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'first_name', 'last_name', 'father_name',
    'email', 'phone', 'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'phone', 'email',
  ];

  /**
   * Method to verify phone number
   *
   * @return $this
  */
  public function verifyPhone(): User {
    $this->phone_verified = true;
    $this->save();
    return $this;
  }

  /**
   * Set phone
   *
   * @param string $phone
   * @param bool $verified
   *
   * @return $this
  */
  public function setPhone(string $phone, bool $verified = false): User {
    $this->phone = $phone;
    $this->phone_verified = $verified;
    $this->save();

    return $this;
  }

  /**
   * Set password
   *
   * @param string $password
   *
   * @return $this
  */
  public function setPassword(string $password): User {
    $this->password = Hash::make($password);
    $this->save();

    return $this;
  }

  /**
   * Relations
  */

  /**
   * Relation to profile
   *
   * @return HasOne
  */
  public function profile(): HasOne {
    return $this->hasOne(Profile::class);
  }

  /**
   * Relation to favourite services
   *
   * @return BelongsToMany
  */
  public function favouriteServices(): BelongsToMany {
    return $this->belongsToMany(ProfileSpeciality::class, 'user_favourite_services', 'user_id', 'service_id');
  }

  /**
   * Relation to cards
   *
   * @return HasMany
  */
  public function cards(): HasMany {
    return $this->hasMany(Card::class, 'user_id');
  }

  /**
   * Scopes
  */

  /**
   * Scope by verified users
   *
   * @param Builder $query
   * @param bool $verified
   *
   * @return Builder
  */
  public function scopeVerified(Builder $query, bool $verified = true): Builder {
    return $query->where('phone_verified', $verified);
  }

  /**
   * Scope by phone
   *
   * @param Builder $query
   * @param string $phone
   *
   * @return Builder
  */
  public function scopePhone(Builder $query, string $phone): Builder {
    return $query->where('phone', $phone);
  }

  /**
   * Scope by email
   *
   * @param Builder $query
   * @param string $email
   *
   * @return Builder
  */
  public function scopeEmail(Builder $query, string $email): Builder {
    return $query->where('email', $email);
  }

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims(): array
  {
    return [];
  }

  public function routeNotificationForNexmo($notification)
  {
    return $this->phone;
  }
}
