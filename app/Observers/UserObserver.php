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
    $fields = $user::getSynchFields();
    if ($user->isDirty($fields) && $profile) {
      foreach ($fields as $field) {
        $profile->{$field} = $user->{$field};
        $profile->save();
      }
    }
  }
}
