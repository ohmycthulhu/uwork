<?php

use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Get profiles and users
    $profiles = \App\Models\User\Profile::all();
    $users = \App\Models\User::all();

    // Create several complaints for each profile
    foreach ($profiles as $profile) {
      $complaints = $users->filter(function ($u) use ($profile) {
        return $u->id !== $profile->user_id;
      })->random(rand(1, 5))
        ->map(function ($user) {
          return factory(\App\Models\Complaints\Complaint::class)
            ->make(['user_id' => $user->id]);
        });

      $profile->complaints()
        ->createMany($complaints->toArray());
    }
  }
}
