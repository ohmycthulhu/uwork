<?php

use App\Models\Categories\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    factory(Category::class, 5)
      ->create()
      ->each(function ($c) {
        $c->children()
          ->createMany(
            factory(Category::class, 5)
              ->make()
              ->toArray()
          )->each(function ($c) {
            $c->children()
              ->createMany(
                factory(Category::class, 5)
                  ->make()
                  ->toArray()
              );
          });
      });
  }
}
