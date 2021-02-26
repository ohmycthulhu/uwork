<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      if (config('app.env') == 'production') {
        $this->call(ActualRegionsSeeder::class);
        $this->call(CategoriesSeeder::class);
      } else {
        $this->call(UserSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(ProfileSeeder::class);
      }
    }
}
