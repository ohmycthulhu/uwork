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
    $users = \App\Models\User::query()
      ->with('profile')
      ->get();

    foreach ($users as $user) {
      if (!$user->profile()->first()) {
        continue;
      }
      $user->notifications()
        ->createMany(
          factory(\App\Models\User\Notification::class, rand(3, 15))
            ->make([
              'notifiable_type' => \App\Models\User\Profile::class,
              'notifiable_id' => $user->profile()->first()->id,
            ])
            ->toArray()
        );
    }
  }
}
