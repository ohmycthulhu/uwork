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
