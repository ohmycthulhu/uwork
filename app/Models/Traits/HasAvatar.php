<?php


namespace App\Models\Traits;

use App\Facades\MediaFacade;
use App\Models\Media\Image;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait HasAvatar
{
  /**
   * Method to change avatar
   *
   * @param UploadedFile $image
   *
   * @return $this
   */
  public function setAvatar(UploadedFile $image): self
  {
    $this->removeAvatar();

    if (!Str::startsWith($image->getMimeType(), "image") && false) {
      return $this;
    }

    $fileName = Str::random() . ".{$image->getClientOriginalExtension()}";

    try {
      Storage::disk('public')
        ->copy($image->getPath(), "avatars/$fileName");

      $this->{$this->avatarColumn} = "avatars/$fileName";
      $this->save();
    } catch (Exception $e) {
      Log::error("Error on saving user avatar - ".$e->getMessage());
      throw $e;
    }

    MediaFacade::upload(
      $image,
      'avatar',
      static::class,
      $this->id
    );

    return $this;
  }

  /**
   * Function to remove avatar
   *
   * @return $this
  */
  public function removeAvatar(): self {
    if ($this->{$this->avatarColumn}) {
      $this->{$this->avatarColumn} = null;
      $this->save();
    }
    $this->avatarImage()
      ->delete();
    return $this;
  }

  /**
   * Morph image for avatar
   *
   * @return MorphOne
  */
  public function avatarImage(): MorphOne {
    return $this->morphOne(Image::class, 'model');
  }

  /**
   * Avatar attribute
   *
   * @return ?string
  */
  public function getAvatarUrlAttribute(): ?string {
    $path = $this->getAvatarPathAttribute();
    return $path ? URL::to($path) : null;
  }

  /**
   * Avatar attribute
   *
   * @return ?string
  */
  public function getAvatarPathAttribute(): ?string {
    $url = $this->{$this->avatarColumn};
    return $url ? Storage::url($url) : null;
  }
}