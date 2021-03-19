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
        $this->call(RealCategoriesSeeder::class);
      } else {
//        $this->call(CategoriesSeeder::class);
        $this->call(RealCategoriesSeeder::class);
        $this->call(ActualRegionsSeeder::class);
        $this->call(UserSeeder::class);
//        $this->call(LocationSeeder::class);
        $this->call(ProfileSeeder::class);
        $this->call(AdministratorSeeder::class);
        $this->call(MessengerSeeder::class);
        $this->call(InfoSeeder::class);
      }
    }
}
