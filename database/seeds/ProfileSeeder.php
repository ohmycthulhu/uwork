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
          $profile = $user->profile()
            ->create(factory(\App\Models\User\Profile::class)->make()->toArray());

          foreach ($categories->shuffle()->slice(0, 3) as $category) {
            $profile->addSpeciality($category->id, rand(100, 5000) / 10);
          }
        }
    }
}
