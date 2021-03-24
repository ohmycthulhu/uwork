<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\User\Notification::class, function (Faker $faker) {
  return [
    'title' => $faker->words(4, true),
    'description' => $faker->boolean ? $faker->text : null,
  ];
});
