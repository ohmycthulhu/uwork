<?php

use App\Models\User\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all();
        $categories = \App\Models\Categories\Category::all();

        foreach ($users as $user) {
          $p = factory(\App\Models\User\Profile::class)
            ->make()
            ->toArray();
          unset($p['is_approved']);
          /* @var Profile $profile */
          $profile = $user->profile()->first() ??
            $user->profile()->create($p);

          foreach ($categories->shuffle()->slice(0, 3) as $category) {
            $profile->addSpeciality($category->id, rand(100, 5000) / 10, Str::random());
          }

          foreach ($users->shuffle()->take(3) as $u) {
            $profile->reviews()
              ->create(
                factory(\App\Models\Profile\Review::class)
                  ->make(['user_id' => $u->id, 'speciality_id' => $profile->specialities()->inRandomOrder()->first()->id])
                  ->toArray()
              );
          }

          $profile->views()
            ->createMany(
              factory(\App\Models\Profile\ProfileView::class, 15)
                ->make()
                ->toArray()
            );

          $profile->synchronizeViews();
          $profile->synchronizeReviews();
        }
    }
}
