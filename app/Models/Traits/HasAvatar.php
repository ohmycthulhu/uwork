<?php


namespace App\Models\Traits;


use App\Models\User\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use \Exception;

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
    if (!Str::startsWith($image->getMimeType(), "image") && false) {
      return $this;
    }

    $fileName = Str::random() . ".{$image->getClientOriginalExtension()}";

    try {
      Storage::disk('public')
        ->put("avatars/$fileName", File::get($image));

      $this->{$this->avatarColumn} = "avatars/$fileName";
      $this->save();
    } catch (Exception $e) {
      Log::error("Error on saving user avatar - ".$e->getMessage());
    }

    return $this;
  }

  /**
   * Avatar attribute
   *
   * @return ?string
  */
  public function getAvatarUrlAttribute(): ?string {
    $url = $this->{$this->avatarColumn};
    return $url ? Storage::url("storage/$url") : null;
  }

  /**
   * Avatar attribute
   *
   * @return ?string
  */
  public function getAvatarPathAttribute(): ?string {
    $url = $this->{$this->avatarColumn};
    return $url ? "/storage/$url" : null;
  }
}