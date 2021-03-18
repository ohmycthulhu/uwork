<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
  /**
   * Handle the user "updated" event.
   *
   * @param \App\Models\User $user
   * @return void
   */
  public function updating(User $user)
  {
    $profile = $user->profile()->first();
    if ($user->isDirty(['region_id', 'city_id', 'district_id']) && $profile) {
      $profile->region_id = $user->region_id;
      $profile->city_id = $user->city_id;
      $profile->district_id = $user->district_id;
      $profile->save();
    }
  }
}
