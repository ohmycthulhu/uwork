<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Profile\Review::class, function (Faker $faker) {
  return [
    'headline' => $faker->words(5, true),
    'text' => $faker->text,
    'ip_addr' => $faker->ipv4,
    'rating_quality' => $faker->numberBetween(1, 5),
    'rating_time' => $faker->numberBetween(1, 5),
    'rating_price' => $faker->numberBetween(1, 5),
  ];
});

$factory->define(\App\Models\Profile\ProfileView::class, function (Faker $faker) {
  return [
    'ip_addr' => $faker->ipv4,
    'opened' => $faker->boolean,
  ];
});
