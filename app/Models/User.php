<?php /** @noinspection PhpUnusedParameterInspection */

namespace App\Models;

use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Location\Region;
use App\Models\Location\Subway;
use App\Models\Messenger\Chat;
use App\Models\Payment\Card;
use App\Models\Profile\Review;
use App\Models\Traits\HasAvatar;
use App\Models\User\Notification;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
  use SoftDeletes, Notifiable, HasAvatar;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'first_name', 'last_name', 'father_name',
    'email', 'phone', 'password',
    'region_id', 'city_id', 'district_id', 'subway_id',
    'birthdate', 'is_male',
  ];

  protected $with = ['avatarImage'];

  protected $avatarColumn = 'avatar';

  protected $casts = [
    'birthdate' => 'date',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'avatar', 'settings',
  ];

  protected $appends = [
    'notification_settings',
    'avatar_url',
    'avatar_path',
  ];

  /**
   * Available settings
   *
   * @var array
   */
  protected $availableSettings = [
    'service_change_email' => [
      'default' => true,
    ],
    'new_service_email' => [
      'default' => true,
    ],
    'important_events_sms' => [
      'default' => true,
    ]
  ];
//
//  /**
//   * Method to verify phone number
//   *
//   * @return $this
//  */
//  public function verifyPhone(): User {
//    $this->phone_verified = true;
//    $this->save();
//    return $this;
//  }

  /**
   * Sets email address
   * @param string $email
   * @return $this
  */
  public function setEmail(string $email): User {
    $this->email = $email;
    $this->save();
    return $this;
  }

  /**
   * Set phone
   *
   * @param string $phone
   *
   * @return $this
  */
  public function setPhone(string $phone): User {
    $this->phone = $phone;
//    $this->phone_verified = $verified;
    $this->save();

    return $this;
  }

  /**
   * Get phone
   *
   * @return string
  */
  public function getPhone(): string {
    return $this->phone;
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
   * Method to set setting
   *
   * @param string $key
   * @param bool $value
   *
   * @return $this
  */
  public function setSetting(string $key, bool $value): User {
    if (isset($this->availableSettings[$key])) {
      $settings = json_decode($this->settings, true) ?? [];

      $settings[$key] = $value;

      $this->settings = json_encode($settings);

    }
    return $this;
  }

  /**
   * Relations
  */

  /**
   * Relation to profile
   *
   * @return HasMany
//   * @return HasOne
  */
  public function profile(): HasMany {
    return $this->hasMany(Profile::class);
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
   * Relation to messages
   *
   * @return Builder
   * */
  public function chats(): Builder {
    return Chat::query()->user($this);
  }

  /**
   * Relation to reviews
   *
   * @return HasMany
  */
  public function reviews(): HasMany {
    return $this->hasMany(Review::class, 'user_id');
  }

  /**
   * Relation to region
   *
   * @return BelongsTo
   */
  public function region(): BelongsTo {
    return $this->belongsTo(Region::class, 'region_id');
  }

  /**
   * Relation to city
   *
   * @return BelongsTo
  */
  public function city(): BelongsTo {
    return $this->belongsTo(City::class, 'city_id');
  }

  /**
   * Relation to district
   *
   * @return BelongsTo
  */
  public function district(): BelongsTo {
    return $this->belongsTo(District::class, 'district_id');
  }

  /**
   * Relation to subway
   *
   * @return BelongsTo
  */
  public function subway(): BelongsTo {
    return $this->belongsTo(Subway::class, 'subway_id');
  }

  /**
   * Relation to notifications
   *
   * @return HasMany
  */
  public function notifications(): HasMany {
    return $this->hasMany(Notification::class, 'user_id');
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
//  public function scopeVerified(Builder $query, bool $verified = true): Builder {
//    return $query->where('phone_verified', $verified);
//  }

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
   * Scope by setting
   *
   * @param Builder $query
   * @param string $key
   * @param bool $value
   *
   * @return Builder
  */
  public function scopeSetting(Builder $query, string $key, bool $value): Builder {
    $setting = $this->availableSettings[$key] ?? null;
    if ($setting) {
      $default = $setting['default'];
      $query->where(function (Builder $q) use ($default, $key, $value) {
        if ($default == $value) {
          $s = $value ? 'false' : 'true';
          $q->where('settings', 'not like', "%\"$key\":$s%");
        } else {
          $s = $value ? 'true' : 'false';
          $q->where('settings', 'like', "%\"$key\":$s%");
        }
      });
    }
    return $query;
  }

  /**
   * Attribute to parse settings
   *
   * @return array
  */
  public function getNotificationSettingsAttribute(): array {
    $settings = json_decode($this->settings, true) ?? [];
    $result = [];
    foreach ($this->availableSettings as $key => $info) {
      $result[$key] = isset($settings[$key]) ? !!$settings[$key] : $info['default'];
    }
    return $result;
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

  public function routeNotificationForNutnetSms($notification)
  {
    return str_replace(['-', '+', '(', ')', '.'], "", $this->phone ?? '');
  }
}
