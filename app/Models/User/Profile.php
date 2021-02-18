<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Profile extends Model implements HasMedia
{
  use SoftDeletes, HasMediaTrait;

  // Fillable fields
  protected $fillable = [
    'about', 'phone', 'picture'
  ];

  // Hidden fields

  /**
   * Methods
   */

  /**
   * Method to set the phone
   *
   * @param string $phone
   * @param bool $verified
   *
   * @return $this
   */
  public function setPhone(string $phone, bool $verified): Profile
  {
    $this->phone = $phone;
    $this->phone_verified = $verified;
    $this->save();
    return $this;
  }

  /**
   * Method to add specialities
   *
   * @param int $categoryId
   * @param float $price
   *
   * @return Model
   */
  public function addSpeciality(int $categoryId, float $price): Model
  {
    return $this->specialities()
      ->create(['category_id' => $categoryId, 'price' => $price]);
  }

  /**
   * Method to remove speciality
   *
   * @param int $categoryId
   *
   * @return int
   */
  public function removeSpeciality(int $categoryId): int
  {
    return $this->specialities()
      ->categoryId($categoryId)
      ->delete();
  }

  /**
   * Method to change avatar
   *
   * @param UploadedFile $image
   *
   * @return $this
   */
  public function setAvatar(UploadedFile $image): Profile
  {
    $fileName = Str::random() . ".{$image->getClientOriginalExtension()}";

    try {
      Storage::disk('public')
        ->put("avatars/$fileName", \Illuminate\Support\Facades\File::get($image));
    } catch (\Exception $e) {
      throw $e;
    }

    $this->picture = "avatars/$fileName";
    $this->save();

    return $this;
  }

  /**
   * Relations
   */
  /**
   * Relation to user
   *
   * @return BelongsTo
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  /**
   * Relation to specialities
   *
   * @return HasMany
   */
  public function specialities(): HasMany
  {
    return $this->hasMany(ProfileSpeciality::class, 'profile_id');
  }

  /**
   * Scopes
   */

  /**
   * Scope by visibility
   *
   * @param Builder $query
   * @param bool $visible
   *
   * @return Builder
   */
  public function scopeVisible(Builder $query, bool $visible): Builder
  {
    return $query->where('is_hidden', !$visible);
  }


  /**
   * Attributes
   */
}
