<?php

namespace App\Observers;

use App\Models\User\Profile;

class ProfileObserver
{
  /**
   * Handle the profile "created" event.
   *
   * @param \App\Models\User\Profile $profile
   * @return void
   */
  public function created(Profile $profile)
  {
    $user = $profile->user()->first();
    $profile->region_id = $user->region_id;
    $profile->city_id = $user->city_id;
    $profile->district_id = $user->district_id;
    $profile->save();
  }
}
