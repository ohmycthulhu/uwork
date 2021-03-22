<?php

use App\Models\Info\Faq;
use App\Models\Info\HelpCategory;
use App\Models\Info\HelpItem;
use Illuminate\Database\Seeder;

class InfoSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    factory(Faq::class, 20)->create();

    factory(HelpCategory::class, 10)
      ->create()
      ->each(function (HelpCategory $category) {
        $category->items()
          ->createMany(
            factory(HelpItem::class, rand(10, 30))
              ->make()
              ->toArray()
          );
      });
  }
}
