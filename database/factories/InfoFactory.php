<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Info\Faq::class, function (Faker $faker) {
  return [
    'question' => $faker->words(5, true),
    'answer' => $faker->text(400),
    'order' => $faker->numberBetween(1, 100),
  ];
});

$factory->define(\App\Models\Info\HelpCategory::class, function (Faker $faker) {
  return [
    'name' => $faker->words($faker->numberBetween(2, 4), true),
    'order' => $faker->numberBetween(1, 100),
  ];
});

$factory->define(\App\Models\Info\HelpItem::class, function (Faker $faker) {
  return [
    'name' => $faker->words($faker->numberBetween(3, 10), true),
    'order' => $faker->numberBetween(1, 100),
    'text' => $faker->randomHtml(3, 9),
  ];
});
