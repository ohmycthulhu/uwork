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
        factory(Category::class, 10)
          ->create()
          ->each(function (Category $category) {
            factory(Category::class, 5)
              ->create(['parent_id' => $category->id])
              ->each(function (Category $category) {
                factory(Category::class, 5)
                  ->create(['parent_id' => $category->id, 'is_hidden' => 1]);
              });
          });
    }
}
