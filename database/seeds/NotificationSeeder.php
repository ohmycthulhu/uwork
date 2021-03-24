<?php

use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $users = \App\Models\User::all()->load('profile');

    foreach ($users as $user) {
      $user->notifications()
        ->createMany(
          factory(\App\Models\User\Notification::class, rand(3, 15))
            ->make([
              'notifiable_type' => \App\Models\User\Profile::class,
              'notifiable_id' => $user->profile->id,
            ])
            ->toArray()
        );
    }
  }
}
