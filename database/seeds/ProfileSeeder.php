<?php

use Illuminate\Database\Seeder;

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
          $profile = $user->profile()->first() ??
            $user->profile()->create(factory(\App\Models\User\Profile::class)->make()->toArray());

          foreach ($categories->shuffle()->slice(0, 3) as $category) {
            $profile->addSpeciality($category->id, rand(100, 5000) / 10);
          }

          foreach ($users->shuffle()->take(3) as $u) {
            $profile->reviews()
              ->create(
                factory(\App\Models\Profile\Review::class)
                  ->make(['user_id' => $u->id])
                  ->toArray()
              );
          }

          $profile->views()
            ->createMany(
              factory(\App\Models\Profile\ProfileView::class, 15)
                ->make()
                ->toArray()
            );
        }
    }
}
